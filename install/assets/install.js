new Vue({

    el: "#page-wrapper",

    data: {
        website: {
            name: "",
            domain: ""
        },
        steps: {
            welcome: false,
            requirements: false,
            database: false,
            installation: false,
            complete: false
        },
        active: 'welcome',
        requirements: {},
        database: {
            host: "",
            username: "",
            password: "",
            name: ""
        },
        dbFormInvalid: false,
        validatingDb: false,
        errorMessage: null,
        appFormInvalid: false,
        installing: false
    },

    ready: function () {
        var self = this;

        jQuery.get('./check.php?action=requirements', function (data) {
            self.requirements = data;
        });
    },

    methods: {
        showRequirements: function () {
            this.steps.welcome = true;
            this.active = 'requirements';
        },

        meetsRequirements: function () {
            if (this.requirements.length == 0) {
                return false;
            }


            for (var r in this.requirements) {
                if (! r) {
                    return false;
                }
            }

            return true;
        },

        showDatabaseInfo: function () {
            if (! this.meetsRequirements()) {
                return;
            }

            this.steps.database = false;
            this.steps.requirements = true;
            this.active = 'database';
        },

        showInstallScreen: function () {
            this.steps.database = true;
            this.active = 'installation';
        },

        validateDatabase: function () {
            this.dbFormInvalid = this.$validation.invalid;

            if (this.dbFormInvalid) {
                return false;
            }

            var self = this;

            this.validatingDb = true;

            jQuery.ajax({
                type: 'POST',
                url: './check.php?action=database',
                data: this.database,
                dataType: 'json',
                success: function () {
                    self.showInstallScreen();
                },
                error: function (err) {
                    self.dbFormInvalid = true;
                    self.errorMessage = err.responseJSON.message;
                },
                complete: function () {
                    self.validatingDb = false;
                }
            });
        },

        install: function () {
            this.appFormInvalid = this.$validation1.invalid;

            if (this.appFormInvalid) {
                return false;
            }

            var self = this;
            this.installing = true;
            var data = {
                name: this.website.name,
                domain: this.website.domain,
                db: this.database
            };

            jQuery.ajax({
                type: 'POST',
                url: './check.php?action=install',
                data: data,
                dataType: 'json',
                success: function () {
                    self.showFinishScreen();
                },
                error: function (err) {
                    self.errorMessage = err.responseJSON.message;
                },
                complete: function () {
                    self.installing = false;
                }
            });
        },

        showFinishScreen: function () {
            this.steps.installation = true;
            this.steps.complete = true;
            this.active = 'complete';
        }
    }

});