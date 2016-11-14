<?php


class InstallComponentCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testInstallation(AcceptanceTester $I)
    {
	    $this->loginAdmin($I);
	    $this->installExtension($I);
	    $I->comment('Component successfully installed');
    }

	protected function loginAdmin(AcceptanceTester $I)
	{
		$I->comment('I go to /administrator/');
		$I->amOnPage('/administrator/index.php');
		$I->fillField('#mod-login-username', 'admin');
		$I->fillField('#mod-login-password', 'qweasd');
		$I->click('Log in');
		$I->comment('Check if I see Administrator Control Panel');
		$I->see('Control Panel', '.page-title');
	}

	protected function installExtension(AcceptanceTester $I)
	{
		$I->comment('I go to Extension -> Manager -> Install');
		$I->amOnPage('/administrator/index.php?option=com_installer');
		$I->waitForElement('#package', 10);
		$I->click('#package');
		$I->attachFile('input#install_package','pkg.zip');
		$I->click('#installbutton_package');
		$I->comment('I will wait while installing, but 60 seconds max');
		$I->waitForElement('.alert.alert-success', 120);
		$I->dontSee('.alert.alert-error');
		$I->dontSee('.alert.alert-warning');
	}
}
