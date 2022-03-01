<div class="container wsmgs_welcome_container">
    <h2 class="text-center">Welcome to</h2>
    <h1 class="text-center"><?php echo WSMGS_PlUGIN_NAME ?></h1>
    <a class="wsmgs_button video_link" href="#">Watch video tutorial</a>

    <div class="get_started_container">
        <p>Press the button and follow the steps to sync your product with Google Sheet</p>
        <button class="wsmgs_button get_started_btn">Get started</button>
    </div>
</div>

<div class="container wsmgs_wizard_container">

    <div id="smartwizard">
        <ul class="nav">
            <li>
                <a class="nav-link" href="#step-1">
                    Set URL
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-2">
                    Set ID
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-3">

                    Set Script Code
                </a>
            </li>
            <li>
                <a class="nav-link" href="#step-4">
                    Done
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Step 1 -->
            <div id="step-1" class="tab-pane" role="tabpanel">
                <div class="wsmgs_inputs">

                    <div class="wsmgs_input_container">
                        <h2>Add Google Sheet URL</h2>
                        <p>Copy the URL from your Google Sheet & paste it here. So that, the WordPress system can add
                            all your WooCoommerce Products into it.</p>
                        <label class="input" for="sheetUrl">
                            <span class="input__label">Google Sheet URL&nbsp;
                                <i class="fa-solid fa-circle-info wsmgs_tooltip_element" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="
                                    <div class='tooltip_image_container'>
                                        <p>
                                            Copy your Google Sheet URL & paste it below to get access your products into it
                                        </p>
                                        <img
                                            src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/url-screenshot.png' ?>' />
                                        </div>
                                    ">
                                </i>
                            </span>
                            <input class="input__field modal_sheet_url" type="text" name="sheetUrl" value=""
                                placeholder="Enter your google sheet URL" />
                        </label>
                    </div>

                    <div class="wsmgs_input_container">

                        <h3>Add Tab Name</h3>
                        <p>Copy a Tab Name <code>(ex: Sheet1)</code> from your Google Sheet & paste it below. So that,
                            all your products can be stored in this tab.</p>

                        <label class="input" for="tabName">
                            <span class="input__label">Tab Name&nbsp;
                                <i class="fa-solid fa-circle-info wsmgs_tooltip_element" data-bs-toggle="tooltip"
                                    data-bs-placement="right" title="
                                    <div class='tooltip_image_container'>
                                        <p>
                                            Copy the Tab Name & paste it here. Thus your products will be stored in this tab
                                        </p>
                                        <img
                                            src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/tab-name.png' ?>' />
                                        </div>
                                    ">
                                </i>
                            </span>
                            <input class="input__field modal_tab_name" type="text" name="tabName" value=""
                                placeholder="Enter your Tab Name" />
                        </label>

                    </div>

                </div>
            </div>

            <!-- Step 2 -->
            <div id="step-2" class="tab-pane" role="tabpanel">

                <div class="step2_content_wrapper">
                    <h2>Get the ID to give <code>editor</code> access</h2>
                    <p>You need to add this ID in your Google Sheet by giving <code>editor</code> access. Thus this
                        plugin
                        can control your sheet to sync your WooCommerce products.</p>
                    <ul class="bot_guidelines">
                        <li>Copy the ID below</li>
                        <li>Then, go to your Google Sheet & click <code>Share</code> button at the top-right position
                        </li>
                        <li>Paste the ID and give <code>editor</code> access</li>
                        <li>Then click the <code>Share</code> button to confirm</li>
                    </ul>

                    <div class="bot_id_container">

                        <div class="bot_info">
                            <span class="bot_mail">
                                <code>wcsmgs@wc-stock-management-with-sheet.iam.gserviceaccount.com</code>
                            </span>
                        </div>

                        <button class="bot_copy_btn wsmgs_tooltip_element" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="
                            <div class='tooltip_image_container'>
                                <p>
                                    Copy your Google Sheet URL & paste it below to get access your products into it
                                </p>
                                <img
                                    src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/editor-access.png' ?>' />
                                </div>
                            ">
                            Copy
                        </button>

                    </div>


                    <div class="checkbhox">
                        <input type="checkbox" name="gave_editor_access" id="gave_editor_access">
                        <label for="gave_editor_access">I've gave this ID editor access to my Google Sheet</label>
                    </div>
                </div>

            </div>

            <!-- Step 3 -->
            <div id="step-3" class="tab-pane" role="tabpanel">
                <div class="step3_content_wrapper">


                    <h2>Apply the Script code</h2>
                    <p>
                        You need to add the code in <code>Apps Script</code> section of your Google Sheet. This is our
                        plugin
                        code to manage all the activities provided by this plugin.
                        Please follow the steps mentioned below
                    </p>

                    <ul class="bot_guidelines">
                        <li>Copy the Script Code below</li>
                        <li>Then, got to <code>Extension</code> menu of your Google Sheet</li>
                        <li>Click on <code>Apps Script</code></li>
                        <li>Now remove the existing one and paste the code on it</li>
                        <li>Go to <code>Triggers</code> & click <code>Add Trigger</code></li>
                        <li>Select <code>atEdit</code> option from <code>Choose which function to run</code> dropdown
                        </li>
                        <li>Select <code>On edit</code> option from <code>Select event type dropdown</code> dropdown
                        </li>
                        <li>Click save. If you are doing it for first time than Google will ask you for permission</li>
                        <li>Click advance & give the script permission to work properly</li>
                    </ul>

                    <div class="script_code_container">

                        <div class="placeholder">
                            <div class="code_placeholder" style="width: 100%;"></div>
                            <div class="code_placeholder" style="width: 90%;"></div>
                            <div class="code_placeholder" style="width: 80%;"></div>
                            <div class="code_placeholder" style="width: 95%;"></div>
                            <div class="code_placeholder" style="width: 85%;"></div>
                            <div class="code_placeholder" style="width: 80%;"></div>
                        </div>

                        <button class="script_copy_btn wsmgs_tooltip_element" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="
                            <div class='tooltip_image_container'>
                                <p>
                                    Copy your Google Sheet URL & paste it below to get access your products into it
                                </p>
                                <img
                                    src='<?php echo WSMGS_BASE_URL . 'assets/public/images/tooltips/app-script.png' ?>' />
                                </div>
                        ">
                            Copy Script
                        </button>

                    </div>

                    <div class="checkbhox">
                        <input type="checkbox" name="pasted_app_script" id="pasted_app_script">
                        <label for="pasted_app_script">I've pasted App Script code & saved accordingly</label>
                    </div>

                </div>

            </div>

            <!-- Step 4 -->
            <div id="step-4" class="tab-pane" role="tabpanel">
                <div class="step4_content_wrapper">
                    <h1 class="text-center">Sync with Google Sheet</h1>
                    <p>
                        Press the button to Sync your WooCommerce Products with your given Google Sheet. Thus, all
                        products will be stored
                        in your Google Sheet automatically.
                    </p>

                    <button class="wsmgs_button sync_button">Sync with Google Sheet</button>
                </div>
            </div>
        </div>
    </div>

</div>