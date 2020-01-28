# onetimesecret
Extension for TYPO3, according to [onetimesecret.com](https://onetimesecret.com/) 

## What does it do?

* Create one time links to secret phrases, e.g. passwords you don't want to sent via email
* optinally inform admin, if secret was successfully delivered

## Install
* Install via extension manager or
* Install via composer
* Include static template

## Configuration
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Description</th>
<th>Type, Validation</th>
<th>Default</th>
</tr>
</thead>
  <tr>
    <th align="left">adminEmail</th>
    <td align="left">Email address for admin mails, leave blank, if you do not want to get informed</td>
    <td>string, email</td>
    <td></td>
  </tr>
  <tr>
    <th align="left">adminName</th>
    <td align="left">Name in admin mails</td>
    <td>string</td>
    <td></td>
  </tr>
  <tr>
    <th align="left">overrideFlexformSettingsIfEmpty</th>
    <td align="left">Fields, which sould be overridden from TypoScript if left blank in the flexform (like in tx_news, thx to Georg Ringer!).</td>
    <td>string</td>
    <td>adminName</td>
  </tr>
</table>

## Site config (for nicer link)

```yaml
routeEnhancers:
  Onetimesecret:
    type: Extbase
    extension: Onetimesecret
    plugin: Onetimesecret
    routes:
      -
        routePath: '/showsecret/{token}/{uid}'
        _controller: 'Onetimesecret::showSecret'
        _arguments:
          token: token
          uid: uid
```

***

# To do
- write a scheduler task to remove expired link
