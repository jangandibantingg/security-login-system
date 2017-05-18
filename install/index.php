<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Installation | Advanced Security</title>

    <link rel='stylesheet' href='../assets/css/bootstrap.min.css' type='text/css' />
    <link rel='stylesheet' href='../assets/css/font-awesome.min.css' type='text/css' />
    <link rel='stylesheet' href='./assets/install.css' type='text/css' />
</head>
<body>

<div id="page-wrapper" v-cloak>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 logo-wrapper">
                <img src="../assets/img/logo-with-text.png" alt="Advanced Security" class="logo">
            </div>
        </div>
        <div class="wizard col-md-6 col-md-offset-3">
            <div class="steps">
                <ul>
                    <li>
                        <a :class="{selected: active == 'welcome', done: steps.welcome}">
                            <div class="stepNumber"><i class="fa fa-home"></i></div>
                            <span class="stepDesc text-small">Welcome</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{selected: active == 'requirements', done: steps.requirements}">
                            <div class="stepNumber"><i class="fa fa-list"></i></div>
                            <span class="stepDesc text-small">System Requirements</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{selected: active == 'database', done: steps.database}">
                            <div class="stepNumber"><i class="fa fa-database"></i></div>
                            <span class="stepDesc text-small">Database Info</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{selected: active == 'installation', done: steps.installation}">
                            <div class="stepNumber"><i class="fa fa-terminal"></i></div>
                            <span class="stepDesc text-small">Installation</span>
                        </a>
                    </li>
                    <li>
                        <a :class="{done: steps.complete}">
                            <div class="stepNumber"><i class="fa fa-flag-checkered"></i></div>
                            <span class="stepDesc text-small">Complete</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="step-content" v-if="active == 'welcome'">
                <h3>Welcome</h3>
                <hr>
                <p>This steps will guide you through few step installation process.</p>
                <p>When this installation process is finished, you will be able
                    to login and manage your users immediately! </p>
                <br>
                <a href="javascript:;" @click="showRequirements" class="btn btn-as pull-right" type="button">
                    Next
                    <i class="fa fa-arrow-right"></i>
                </a>
                <div class="clearfix"></div>
            </div>

            <div v-if="active == 'requirements'">
                <div class="alert alert-danger" v-if="! meetsRequirements()">
                    <strong>Oh snap!</strong> Your system does not meet the requirements. You have to fix them in order to continue.
                </div>

                <div class="step-content">
                    <h3>System Requirements</h3>
                    <hr>
                    <ul class="list-group">
                        <li v-for="(requirement, loaded) in requirements"
                            class="list-group-item"
                            :class="{'list-group-item-danger' : ! loaded}">
                            {{ requirement }}
                            <span class="badge badge-success" v-if="loaded"><i class="fa fa-check"></i></span>
                            <span class="badge badge-danger" v-if="! loaded"><i class="fa fa-times"></i></span>
                        </li>
                    </ul>

                    <a class="btn btn-as pull-right"
                       @click="showDatabaseInfo"
                       href="javascript:;"
                       :disabled="! meetsRequirements()">
                        Next
                        <i class="fa fa-arrow-right"></i>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="step-content" v-if="active == 'database'">
                <h3>Database Info</h3>
                <hr>
                <validator name="validation">
                    <div class="alert alert-danger" v-if="dbFormInvalid">
                        <ul>
                            <li v-if="errorMessage">{{ errorMessage }}</li>
                            <li v-if="$validation.host.required">Database Host is required.</li>
                            <li v-if="$validation.username.required">Database Username is required.</li>
                            <li v-if="$validation.password.required">Database Password is required.</li>
                            <li v-if="$validation.database.required">Database Name is required.</li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label for="host">Host</label>
                        <input type="text" class="form-control"
                               v-model="database.host" v-validate:host="['required']">
                        <small>Database host. Usually you should enter localhost or mysql.</small>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control"
                               v-model="database.username" v-validate:username="['required']">
                        <small>Your database username.</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control"
                               v-model="database.password" v-validate:password="['required']">
                        <small>Database password for provided username.</small>
                    </div>
                    <div class="form-group">
                        <label for="database">Database Name</label>
                        <input type="text" class="form-control"
                               v-model="database.name" v-validate:database="['required']">
                        <small>Name of database where tables should be created.</small>
                    </div>
                </validator>
                <button class="btn btn-as pull-right" @click="validateDatabase" :disabled="validatingDb">
                    <span v-if="! validatingDb">
                        Next
                        <i class="fa fa-arrow-right"></i>
                    </span>
                    <span v-if="validatingDb">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                        Connecting...
                    </span>
                </button>
                <div class="clearfix"></div>
            </div>

            <div class="step-content" v-if="active == 'installation'">
                <h3>Installation</h3>
                <hr>
                <validator name="validation1">
                    <div class="alert alert-danger" v-if="appFormInvalid">
                        <ul>
                            <li v-if="$validation1.name.required">Website name is required.</li>
                            <li v-if="$validation1.domain.required">Website domain is required.</li>
                        </ul>
                    </div>
                    <p>Advanced Security is ready to be installed!</p>
                    <p>
                        Provide your website name and domain below and start the installation by clicking the
                        "Install" button. <br>It should not take more than few seconds.
                    </p>
                    <div class="form-group">
                        <label for="website_name">Website Name</label>
                        <input type="text" class="form-control" id="website_name"
                               v-model="website.name" value="Advanced Security" v-validate:name="['required']">
                    </div>
                    <div class="form-group">
                        <label for="domain">Website Domain</label>
                        <input type="text" class="form-control"
                               v-model="website.domain" value="<?php echo $_SERVER['HTTP_HOST']; ?>"
                               v-validate:domain="['required']">
                        <small>
                            Your website domain (if script doesn't guess it correctly). If you are installing this script
                            in a subfolder, <strong>DO NOT</strong> write path to that subfolder here!
                            So, just your website domain like google.com or codecanyon.com.
                        </small>
                    </div>
                    <button class="btn btn-as pull-right" @click="install">
                        <span v-if="! installing">
                               <i class="fa fa-play"></i>
                            Install
                        </span>
                            <span v-if="installing">
                            <i class="fa fa-circle-o-notch fa-spin"></i>
                            Installing...
                        </span>
                    </button>
                </validator>
                <div class="clearfix"></div>
            </div>

            <div class="step-content" v-if="active == 'complete'">
                <h3>Complete!</h3>
                <hr>
                <p><strong>Well Done!</strong></p>
                <p>
                    You application is now successfully installed!
                    You can login by clicking on "Log In" button below.
                </p>

                <p>
                    <strong>Important!</strong> Since your ASEngine directory is still writable,
                    you can change the permissions to 755 to make it writable only by root user.
                </p>

                <br>

                <a class="btn btn-as pull-right" href="../login.php">
                    <i class="fa fa-sign-in"></i>
                    Log In
                </a>
                <div class="clearfix"></div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="./assets/vue.js"></script>
<script type="text/javascript" src="./assets/vue-validator.min.js"></script>
<script type="text/javascript" src="./assets/install.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
</script>
</body>
</html>
