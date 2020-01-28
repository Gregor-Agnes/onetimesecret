<?php

namespace Zwo3\Onetimesecret\Controller;

use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use Zwo3\NewsletterSubscribe\Domain\Model\Subscription;
use Zwo3\Onetimesecret\Utilities\OverrideEmptyFlexformValues;
use Zwo3\Onetimesecret\Domain\Model\Onetimesecret;
use Zwo3\Onetimesecret\Domain\Repository\OnetimesecretRepository;

/**
 * Class SubscribeController
 *
 * @package Zwo3\NewsletterSubscribe\Controller
 */
class OnetimesecretController extends ActionController
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var OnetimesecretRepository
     */
    protected $onetimesecretRepository;

    /**
     * @var OverrideEmptyFlexformValues
     */
    protected $overrideFlexFormValues;

    /**
     * @var PasswordHashInterface
     */
    protected $passwordHashing;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function initializeAction()
    {

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->onetimesecretRepository = $this->objectManager->get(OnetimesecretRepository::class);
        $this->configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        $this->overrideFlexFormValues = $this->objectManager->get(OverrideEmptyFlexformValues::class);
        $this->passwordHashing = $this->objectManager->get(PasswordHashFactory::class)
            ->getDefaultHashInstance('FE');
        $this->settings = $this->overrideFlexFormValues->overrideSettings('onetimesecret', 'Onetimesecret');
    }

    public function showFormAction()
    {
        $formToken = FormProtectionFactory::get('frontend')
            ->generateToken('Onetimesecret', 'showForm', $this->configurationManager->getContentObject()->data['uid']);

        $this->view->assignMultiple([
            'formToken' => $formToken,
        ]);
    }

    /**
     * @param Onetimesecret $onetimesecret
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function createLinkAction(Onetimesecret $onetimesecret)
    {
        if (!FormProtectionFactory::get('frontend')
            ->validateToken(
                (string)GeneralUtility::_POST('formToken'),
                'Onetimesecret', 'showForm', $this->configurationManager->getContentObject()->data['uid']
            )) {
            $this->redirect('showForm');
        }
        $validUntil = new \DateTime();
        if (intval(GeneralUtility::_POST()['ttl']) > 604800 || intval(GeneralUtility::_POST()['ttl']) < 1) {
            $onetimesecret->setValidUntil($validUntil->setTimestamp(time() + 604800));
        } else {
            $onetimesecret->setValidUntil($validUntil->setTimestamp(time() + intval(GeneralUtility::_POST()['ttl'])));
        }
        $key = hash('sha256', $onetimesecret->getSecret() . random_bytes(32));
        $onetimesecret->setCrdate(new \DateTime());
        $onetimesecret->setToken($this->passwordHashing->getHashedPassword($key));
        $onetimesecret->setSecret($this->safeEncrypt($onetimesecret->getSecret(), hex2bin($key)));
        $onetimesecret->setPid($this->onetimesecretRepository->createQuery()
            ->getQuerySettings()
            ->getStoragePageIds()[0]);

        $this->onetimesecretRepository->add($onetimesecret);

        $this->persistenceManager->persistAll();

        $this->view->assignMultiple(compact('onetimesecret', 'key'));
    }

    /**
     * @param int $uid
     * @param string $token
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws UnknownObjectException
     */
    public function showSecretAction(?int $uid = null, ?string $token = null)
    {

        /** @var Onetimesecret $onetimesecret */
        $onetimesecret = $this->onetimesecretRepository->findByUid($uid);

        if ($onetimesecret) {
            if ($this->passwordHashing->checkPassword($token, $onetimesecret->getToken())) {
                if ($onetimesecret->getValidUntil()->getTimestamp() > time()) {
                    $secret = $this->safeDecrypt($onetimesecret->getSecret(), hex2bin($token));
                    if($this->settings['adminEmail']) {
                        try {
                            $this->sendTemplateEmail(
                                [$this->settings['adminEmail'] => $this->settings['adminName']],
                                [$this->settings['adminEmail'] => $this->settings['adminName']],
                                LocalizationUtility::translate('emailSuccess', 'onetimesecret'),
                                'Mail/' . $GLOBALS['TSFE']->sys_language_isocode . '/SecretShown',
                                [

                                ]
                            );
                        } catch (InvalidTemplateResourceException $exception) {
                            $this->addFlashMessage('Create a template in the Mail Folder for the current language (e.g. de, fr, dk).', 'No E-Mail-Template found', AbstractMessage::ERROR);
                        }
                    }
                    $this->onetimesecretRepository->remove($onetimesecret);
                    $this->persistenceManager->persistAll();

                    $this->redirect('success', null, null, compact('secret'));

                } else {
                    $this->onetimesecretRepository->remove($onetimesecret);
                    $this->persistenceManager->persistAll();
                    $this->redirect('unsuccess', null, null, null, null, null, 403);
                }
                // secret remove, don't care, if it's to old or not

            } else {

                // increasing sleeptimer to prevent bruteforce
                $onetimesecret = $this->setSleep($onetimesecret, 300, 2);
                $this->onetimesecretRepository->update($onetimesecret);

                $this->persistenceManager->persistAll();

                $this->redirect('unsuccess', null, null, null, null, null, 403);

            }
        } else {
            $this->redirect('unsuccess', null, null, null, null, null, 403);
        }
    }

    /**
     * @param string $secret
     */
    public function successAction($secret)
    {
        $this->view->assignMultiple(compact('secret'));
    }

    public function unsuccessAction()
    {
        $this->view->assignMultiple([]);
    }

    /**
     * @param array $recipient recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     * @param array $replyTo replyTo Address
     * @return boolean TRUE on success, otherwise false
     */
    protected function sendTemplateEmail(array $recipient, array $sender, $subject, $templateName = 'Mail/Default', array $variables = array(), array $replyTo = null, array $attachments = [])
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $emailView = GeneralUtility::makeInstance(StandaloneView::class);
        $emailView->setControllerContext($this->controllerContext);
        $emailView->setTemplate($templateName);

        $emailView->assignMultiple($variables);
        $emailBody = $emailView->render();

        /** @var $message \TYPO3\CMS\Core\Mail\MailMessage */
        $message = new MailMessage();
        $message->setTo($recipient)
            ->setFrom($sender)
            ->setSubject($subject)
        ;

        if ($replyTo) {
            $message->setReplyTo($replyTo);
        }

        // Possible attachments here
        foreach ($attachments as $attachment) {
            $message->attach($attachment);
        }

        // HTML Email
        $message->setBody($emailBody, 'text/html');

        // Add TXT Part
        #$message->addPart($emailBodyTxt, 'text/plain');

        $message->send();

        return $message->isSent();
    }

    /**
     * @param Onetimesecret $onetimesecret
     * @param int $maxSleeptime max time to wait after last hit, if reached, sleep is resetted
     * @param int $multiplier multipliere * hitnumber = seconds to wait,
     * @return Onetimesecret
     */
    protected function setSleep(Onetimesecret $onetimesecret, $maxSleeptime = 300, $multiplier = 2): Onetimesecret
    {
        $sleepTime = $onetimesecret->getHitNumber() * $multiplier;
        if (time() > $onetimesecret->getLastHit() + $maxSleeptime) {
            // reset sleep after 5 minutes
            $sleepTime = 0;
            $onetimesecret->setHitNumber(0);
        } else {
            $onetimesecret->setHitNumber($onetimesecret->getHitNumber() + 1);
        }
        sleep($sleepTime);

        $onetimesecret->setLastHit(time());

        return $onetimesecret;
    }

    /**
     * @return bool The flash message or FALSE if no flash message should be set
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }

    /**
     * Encrypt a message
     *
     * @param string $message - message to encrypt
     * @param string $key - encryption key
     * @return string
     * @throws RangeException
     */
    function safeEncrypt(string $message, string $key): string
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce .
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);

        return $cipher;
    }

    /**
     * Decrypt a message
     *
     * @param string $encrypted - message encrypted with safeEncrypt()
     * @param string $key - encryption key
     * @return string
     * @throws Exception
     */
    function safeDecrypt(string $encrypted, string $key): string
    {
        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if (!is_string($plain)) {
            throw new Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);

        return $plain;
    }

}