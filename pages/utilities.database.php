<?php

declare(strict_types=1);

/**
 * Teampass - a collaborative passwords manager.
 * ---
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * ---
 *
 * @project   Teampass
 * @file      utilities.database.php
 * ---
 *
 * @author    Nils Laumaillé (nils@teampass.net)
 *
 * @copyright 2009-2023 Teampass.net
 *
 * @license   https://spdx.org/licenses/GPL-3.0-only.html#licenseText GPL-3.0
 * ---
 *
 * @see       https://www.teampass.net
 */
Use TeampassClasses\SuperGlobal\SuperGlobal;
Use TeampassClasses\NestedTree\NestedTree;
Use TeampassClasses\PerformChecks\PerformChecks;

// Load functions
require_once __DIR__.'/../sources/main.functions.php';

// init
loadClasses('DB');

// Load config if $SETTINGS not defined
try {
    include_once __DIR__.'/../includes/config/tp.config.php';
} catch (Exception $e) {
    throw new Exception("Error file '/includes/config/tp.config.php' not exists", 1);
}

// Do checks
$checkUserAccess = new PerformChecks(
    dataSanitizer(
        [
            'type' => isset($_POST['type']) === true ? $_POST['type'] : '',
        ],
        [
            'type' => 'trim|escape',
        ],
    ),
    [
        'user_id' => isset($_SESSION['user_id']) === false ? null : $_SESSION['user_id'],
        'user_key' => isset($_SESSION['key']) === false ? null : $_SESSION['key'],
        'CPM' => isset($_SESSION['CPM']) === false ? null : $_SESSION['CPM'],
    ]
);
// Handle the case
$checkUserAccess->caseHandler();
if ($checkUserAccess->checkSession() === false || $checkUserAccess->userAccessPage('utilities.database') === false) {
    // Not allowed page
    $_SESSION['error']['code'] = ERR_NOT_ALLOWED;
    include $SETTINGS['cpassman_dir'] . '/error.php';
    exit;
}

// Load language file
require_once $SETTINGS['cpassman_dir'].'/includes/language/'.$_SESSION['user']['user_language'].'.php';

// Define Timezone
date_default_timezone_set(isset($SETTINGS['timezone']) === true ? $SETTINGS['timezone'] : 'UTC');

// Set header properties
header('Content-type: text/html; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// --------------------------------- //

?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0 text-dark"><i class="fas fa-database mr-2"></i><?php echo langHdl('database'); ?></h1>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->


<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#in_edition" aria-controls="in_edition" aria-selected="true"><?php echo langHdl('db_items_edited'); ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#logged_in" role="tab" aria-controls="logged_in" aria-selected="false"><?php echo langHdl('db_users_logged'); ?></a>
                            </li>
                        </ul>


                        <div class="tab-content mt-1" id="myTabContent">
                            <div class="tab-pane fade show active" id="in_edition" role="tabpanel" aria-labelledby="in_edition-tab">
                                <table class="table table-striped table-responsive" id="table-in_edition" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th style=""></th>
                                            <th style=""><?php echo langHdl('item_edition_start_hour'); ?></th>
                                            <th style=""><?php echo langHdl('user'); ?></th>
                                            <th style=""><?php echo langHdl('label'); ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="logged_in" role="tabpanel" aria-labelledby="logged_in-tab">
                                <table class="table table-striped table-responsive" id="table-logged_in" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th style=""></th>
                                            <th style=""><?php echo langHdl('user'); ?></th>
                                            <th style=""><?php echo langHdl('role'); ?></th>
                                            <th style=""><?php echo langHdl('login_time'); ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
