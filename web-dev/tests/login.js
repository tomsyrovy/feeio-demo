module.exports = {
    'Uživatel se loguje': function (test) {
        test
        .open('http://localhost1:8888/web/app_dev.php')
        .assert.title().is('Přihlášení - Feeio', 'Jsem na správné login stránce')
        .execute(function () {
            document.getElementById('username').value = '';
        })
        .type('#username', 'michal.uryc@taktiq.com')
        .type('#password', 'password')
        .wait(1000)
        .screenshot('tests/login-vyplneny.png')
        .click('#_submit')
        // .assert.visible('.alert-warning', 'Zobrazil se warning')
        .assert.title().is('Timesheet - Feeio', 'Jsem správně přihlášen')
        .wait(1000)
        .screenshot('tests/jsem-prihlasen.png')
        .done();
    }
};
