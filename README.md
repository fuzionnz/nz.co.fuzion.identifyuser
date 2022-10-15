# Identify User on donation and event pages

Allows anonymous users to lookup thier existing contact in civicrm with OTP verification and load donation/event pages with checksum URL	

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM (*FIXME: Version number*)

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl nz.co.fuzion.identifyuser@https://github.com/FIXME/nz.co.fuzion.identifyuser/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/nz.co.fuzion.identifyuser.git
cv en identifyuser
```

## Usage

Configuration Page is at - `/civicrm/identifyusersetting?reset=1`

To enable User verification on Events -

- Navigate to Event settings page => `Online Registration` tab
- Enable `Enable User Lookup on the page?` checkbox. Save.
- When you load the event page as anonymous, a button appears on the top. 
- It loads a form with the fields from unsupervised dedupe rule.
- User fills the details on the form and based on phone or email value, the extension sends to code as an SMS or email.
- The next page verifies the code using which the page reloads with the checksum link for the contact.

## Known Issues

(* FIXME *)
