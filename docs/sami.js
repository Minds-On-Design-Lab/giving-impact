
(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href=".html">[Global Namespace]</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Account" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Account.html">Account</a>                    </div>                </li>                            <li data-name="class:Account_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Account_model.html">Account_model</a>                    </div>                </li>                            <li data-name="class:Admin" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Admin.html">Admin</a>                    </div>                </li>                            <li data-name="class:Campaign_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Campaign_model.html">Campaign_model</a>                    </div>                </li>                            <li data-name="class:Campaigns" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Campaigns.html">Campaigns</a>                    </div>                </li>                            <li data-name="class:Dashboard" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Dashboard.html">Dashboard</a>                    </div>                </li>                            <li data-name="class:Diagnostics" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Diagnostics.html">Diagnostics</a>                    </div>                </li>                            <li data-name="class:Donate" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Donate.html">Donate</a>                    </div>                </li>                            <li data-name="class:Donation_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Donation_model.html">Donation_model</a>                    </div>                </li>                            <li data-name="class:GI_Model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="GI_Model.html">GI_Model</a>                    </div>                </li>                            <li data-name="class:Hook_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Hook_model.html">Hook_model</a>                    </div>                </li>                            <li data-name="class:Link_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Link_model.html">Link_model</a>                    </div>                </li>                            <li data-name="class:Main" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Main.html">Main</a>                    </div>                </li>                            <li data-name="class:Migrate" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Migrate.html">Migrate</a>                    </div>                </li>                            <li data-name="class:Opportunities" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Opportunities.html">Opportunities</a>                    </div>                </li>                            <li data-name="class:Opportunity_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Opportunity_model.html">Opportunity_model</a>                    </div>                </li>                            <li data-name="class:Supporter_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Supporter_model.html">Supporter_model</a>                    </div>                </li>                            <li data-name="class:Supporters" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Supporters.html">Supporters</a>                    </div>                </li>                            <li data-name="class:Task_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Task_model.html">Task_model</a>                    </div>                </li>                            <li data-name="class:Transaction_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Transaction_model.html">Transaction_model</a>                    </div>                </li>                            <li data-name="class:User_model" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="User_model.html">User_model</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": ".html", "name": "", "doc": "Namespace "},
            
            {"type": "Class",  "link": "Account.html", "name": "Account", "doc": "&quot;Account controller&quot;"},
                                                        {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method___construct", "name": "Account::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method_index", "name": "Account::index", "doc": "&quot;Main action&quot;"},
                    {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method_edit_currency", "name": "Account::edit_currency", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method_edit_org", "name": "Account::edit_org", "doc": "&quot;Edit organization settings.&quot;"},
                    {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method_edit_user", "name": "Account::edit_user", "doc": "&quot;Edit user settings\/&quot;"},
                    {"type": "Method", "fromName": "Account", "fromLink": "Account.html", "link": "Account.html#method_edit_token", "name": "Account::edit_token", "doc": "&quot;Allows user to regenerate API tokens&quot;"},
            
            {"type": "Class",  "link": "Account_model.html", "name": "Account_model", "doc": "&quot;Account model&quot;"},
                                                        {"type": "Method", "fromName": "Account_model", "fromLink": "Account_model.html", "link": "Account_model.html#method___construct", "name": "Account_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Account_model", "fromLink": "Account_model.html", "link": "Account_model.html#method_set_file", "name": "Account_model::set_file", "doc": "&quot;Set new, uploaded file&quot;"},
                    {"type": "Method", "fromName": "Account_model", "fromLink": "Account_model.html", "link": "Account_model.html#method_save_entry", "name": "Account_model::save_entry", "doc": "&quot;Overrides parent class save&lt;em&gt;entry method by automatically generating\n * permalink\n * updated&lt;\/em&gt;at\n * created_at&quot;"},
                    {"type": "Method", "fromName": "Account_model", "fromLink": "Account_model.html", "link": "Account_model.html#method___image_url", "name": "Account_model::__image_url", "doc": "&quot;image_url computed property&quot;"},
                    {"type": "Method", "fromName": "Account_model", "fromLink": "Account_model.html", "link": "Account_model.html#method___thumb_url", "name": "Account_model::__thumb_url", "doc": "&quot;thumb_url computed property&quot;"},
            
            {"type": "Class",  "link": "Admin.html", "name": "Admin", "doc": "&quot;Admin controller&quot;"},
                                                        {"type": "Method", "fromName": "Admin", "fromLink": "Admin.html", "link": "Admin.html#method___construct", "name": "Admin::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Admin", "fromLink": "Admin.html", "link": "Admin.html#method_index", "name": "Admin::index", "doc": "&quot;Main action&quot;"},
                    {"type": "Method", "fromName": "Admin", "fromLink": "Admin.html", "link": "Admin.html#method_hook", "name": "Admin::hook", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Admin", "fromLink": "Admin.html", "link": "Admin.html#method_update_hook", "name": "Admin::update_hook", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Admin", "fromLink": "Admin.html", "link": "Admin.html#method_clear_hook", "name": "Admin::clear_hook", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Campaign_model.html", "name": "Campaign_model", "doc": "&quot;Campaign model&quot;"},
                                                        {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___construct", "name": "Campaign_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___campaign", "name": "Campaign_model::__campaign", "doc": "&quot;Campaign computed property, always returns FALSE&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___campaign_fields", "name": "Campaign_model::__campaign_fields", "doc": "&quot;Custom campaign fields computed property&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___custom_fields", "name": "Campaign_model::__custom_fields", "doc": "&quot;Custom fields computed property&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___donation_levels", "name": "Campaign_model::__donation_levels", "doc": "&quot;Campaign levels computed property&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___image_url", "name": "Campaign_model::__image_url", "doc": "&quot;Image url computed property&quot;"},
                    {"type": "Method", "fromName": "Campaign_model", "fromLink": "Campaign_model.html", "link": "Campaign_model.html#method___thumb_url", "name": "Campaign_model::__thumb_url", "doc": "&quot;Thumbnail url computed property&quot;"},
            
            {"type": "Class",  "link": "Campaigns.html", "name": "Campaigns", "doc": "&quot;Campaigns Controller&quot;"},
                                                        {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method___construct", "name": "Campaigns::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_index", "name": "Campaigns::index", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_view", "name": "Campaigns::view", "doc": "&quot;View single campaign&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit_basic", "name": "Campaigns::edit_basic", "doc": "&quot;Edit basic campaign settings&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit_design", "name": "Campaigns::edit_design", "doc": "&quot;Edit campaign design settings&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit_receipt", "name": "Campaigns::edit_receipt", "doc": "&quot;Edit campaign receipt settings&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit_fields", "name": "Campaigns::edit_fields", "doc": "&quot;Edit campaign custom fields&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit_campaign_fields", "name": "Campaigns::edit_campaign_fields", "doc": "&quot;Edit campaign create fields&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_edit", "name": "Campaigns::edit", "doc": "&quot;Main edit view&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_new_campaign", "name": "Campaigns::new_campaign", "doc": "&quot;Create new campaign&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_donation", "name": "Campaigns::donation", "doc": "&quot;View single donation&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_donation_edit", "name": "Campaigns::donation_edit", "doc": "&quot;Create\/edit single donation&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_donation_refund", "name": "Campaigns::donation_refund", "doc": "&quot;Refund handler&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_donations", "name": "Campaigns::donations", "doc": "&quot;View recent donations. The final URL segment is used as the offset to\npaginate results.&quot;"},
                    {"type": "Method", "fromName": "Campaigns", "fromLink": "Campaigns.html", "link": "Campaigns.html#method_export", "name": "Campaigns::export", "doc": "&quot;Exports donation log to CSV&quot;"},
            
            {"type": "Class",  "link": "Dashboard.html", "name": "Dashboard", "doc": "&quot;Dashboard controller&quot;"},
                                                        {"type": "Method", "fromName": "Dashboard", "fromLink": "Dashboard.html", "link": "Dashboard.html#method___construct", "name": "Dashboard::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Dashboard", "fromLink": "Dashboard.html", "link": "Dashboard.html#method_index", "name": "Dashboard::index", "doc": "&quot;Index handler. Loads &#039;dashboard\/index&#039; view with:&quot;"},
                    {"type": "Method", "fromName": "Dashboard", "fromLink": "Dashboard.html", "link": "Dashboard.html#method_inactive", "name": "Dashboard::inactive", "doc": "&quot;View only inactive campaigns&quot;"},
            
            {"type": "Class",  "link": "Diagnostics.html", "name": "Diagnostics", "doc": "&quot;Diagnostics controller&quot;"},
                                                        {"type": "Method", "fromName": "Diagnostics", "fromLink": "Diagnostics.html", "link": "Diagnostics.html#method___construct", "name": "Diagnostics::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Diagnostics", "fromLink": "Diagnostics.html", "link": "Diagnostics.html#method_index", "name": "Diagnostics::index", "doc": "&quot;Index handler. Loads &#039;diagnostics\/index&#039; view with:&quot;"},
            
            {"type": "Class",  "link": "Donate.html", "name": "Donate", "doc": "&quot;Donate controller&quot;"},
                                                        {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method___construct", "name": "Donate::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method_index", "name": "Donate::index", "doc": "&quot;Index action&quot;"},
                    {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method_checkout", "name": "Donate::checkout", "doc": "&quot;Checkout handler presents the user with the checkout form&quot;"},
                    {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method_process", "name": "Donate::process", "doc": "&quot;Donation process handler. This is a URL callback that processes\nthe donation, sends the request to the Stripe API and records\nthe result. A successful donation will send the user to\ndonate\/XXXXX\/complete, a failure will send the user back to the\ndonation form.&quot;"},
                    {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method_complete", "name": "Donate::complete", "doc": "&quot;Complete donation.&quot;"},
                    {"type": "Method", "fromName": "Donate", "fromLink": "Donate.html", "link": "Donate.html#method_contact", "name": "Donate::contact", "doc": "&quot;Contact is an Ajax callback only. It exists to record if a user wishes\nto be contacted by the org or not. Accepts the donation HASH as the 2nd\nURL segment and &#039;contact&#039; as a BOOLEAN as a POST parameter.&quot;"},
            
            {"type": "Class",  "link": "Donation_model.html", "name": "Donation_model", "doc": "&quot;Donation model&quot;"},
                                                        {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method___construct", "name": "Donation_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method_select_complete", "name": "Donation_model::select_complete", "doc": "&quot;Select complete fetches extra necessary fields that cannot be set\nby model and bulds correct join syntax&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method_save_entry", "name": "Donation_model::save_entry", "doc": "&quot;Overrides parent save_entry method, sets&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method___supporter", "name": "Donation_model::__supporter", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method___campaign", "name": "Donation_model::__campaign", "doc": "&quot;Campaign computed property. Returns parent campaign&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method___opportunity", "name": "Donation_model::__opportunity", "doc": "&quot;Opportunity computed property, returns parent opportunity, if exists&quot;"},
                    {"type": "Method", "fromName": "Donation_model", "fromLink": "Donation_model.html", "link": "Donation_model.html#method___custom_responses", "name": "Donation_model::__custom_responses", "doc": "&quot;Custom responses computed property&quot;"},
            
            {"type": "Class",  "link": "GI_Model.html", "name": "GI_Model", "doc": "&quot;GI_model base&quot;"},
                                                        {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method___construct", "name": "GI_Model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_assign", "name": "GI_Model::assign", "doc": "&quot;Assign database return to object&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_get_public_properties", "name": "GI_Model::get_public_properties", "doc": "&quot;Return array of public properties for model&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_save_entry", "name": "GI_Model::save_entry", "doc": "&quot;Save handler. Will attempt to insert or update, depending\non model values. Allows for fluent interface&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_delete_entry", "name": "GI_Model::delete_entry", "doc": "&quot;Delete handler&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_get_entry", "name": "GI_Model::get_entry", "doc": "&quot;Fetch entry, automatically fetches based on table and ID&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_flatten", "name": "GI_Model::flatten", "doc": "&quot;Flatten model collection into a key-&gt;value array&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_normalize", "name": "GI_Model::normalize", "doc": "&quot;Normalize a string by removing anything by alnums, &#039;.&#039; and &#039;_&#039;&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method_find", "name": "GI_Model::find", "doc": "&quot;Execute select query, return array of model objects. Automatically\ndiscovers table name, but can be overridden.&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method___call", "name": "GI_Model::__call", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "GI_Model", "fromLink": "GI_Model.html", "link": "GI_Model.html#method___get", "name": "GI_Model::__get", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Hook_model.html", "name": "Hook_model", "doc": "&quot;Hook model&quot;"},
                    
            {"type": "Class",  "link": "Link_model.html", "name": "Link_model", "doc": "&quot;Link model&quot;"},
                                                        {"type": "Method", "fromName": "Link_model", "fromLink": "Link_model.html", "link": "Link_model.html#method___construct", "name": "Link_model::__construct", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Main.html", "name": "Main", "doc": "&quot;Main controller&quot;"},
                                                        {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method___construct", "name": "Main::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_index", "name": "Main::index", "doc": "&quot;Index handler. If the user is currently logged in, will redirect\nto the dashboard.&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_login", "name": "Main::login", "doc": "&quot;Login URL callback. Accepts via POST:\n * email String\n * password String&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_logout", "name": "Main::logout", "doc": "&quot;Logout URL callback. Redirects back to site root&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_new_account", "name": "Main::new_account", "doc": "&quot;Create a new accounts. Accepts via POST:&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_forgot", "name": "Main::forgot", "doc": "&quot;Forgot password handler.&quot;"},
                    {"type": "Method", "fromName": "Main", "fromLink": "Main.html", "link": "Main.html#method_forgot_verify", "name": "Main::forgot_verify", "doc": "&quot;Handles the verification of the password change requests. Accepts\nthe change requests HASH as the second URL segment.&quot;"},
            
            {"type": "Class",  "link": "Migrate.html", "name": "Migrate", "doc": "&quot;\n&quot;"},
                                                        {"type": "Method", "fromName": "Migrate", "fromLink": "Migrate.html", "link": "Migrate.html#method___construct", "name": "Migrate::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Migrate", "fromLink": "Migrate.html", "link": "Migrate.html#method_index", "name": "Migrate::index", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Opportunities.html", "name": "Opportunities", "doc": "&quot;Opportunities controller&quot;"},
                                                        {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method___construct", "name": "Opportunities::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_index", "name": "Opportunities::index", "doc": "&quot;Index action handler&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_inactive", "name": "Opportunities::inactive", "doc": "&quot;View only inactive opportunities&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_view", "name": "Opportunities::view", "doc": "&quot;View single opportunity&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_edit", "name": "Opportunities::edit", "doc": "&quot;Edit or create new opportunity&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_promote", "name": "Opportunities::promote", "doc": "&quot;Promote view action&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_new_opportunity", "name": "Opportunities::new_opportunity", "doc": "&quot;New opportunity handler. Uses the existing edit view\nbut handles logic specific to creating new opportunities&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_donations", "name": "Opportunities::donations", "doc": "&quot;Donation view method&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_donation", "name": "Opportunities::donation", "doc": "&quot;View donation handler&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_donation_edit", "name": "Opportunities::donation_edit", "doc": "&quot;Edit donation handler&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_donation_refund", "name": "Opportunities::donation_refund", "doc": "&quot;Refund handler&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_export", "name": "Opportunities::export", "doc": "&quot;Export handler&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_go_export", "name": "Opportunities::go_export", "doc": "&quot;Exports opportunities list to CSV&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_supporters", "name": "Opportunities::supporters", "doc": "&quot;Supporters view method&quot;"},
                    {"type": "Method", "fromName": "Opportunities", "fromLink": "Opportunities.html", "link": "Opportunities.html#method_supporters_remove", "name": "Opportunities::supporters_remove", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Opportunity_model.html", "name": "Opportunity_model", "doc": "&quot;Opportunity model&quot;"},
                                                        {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___construct", "name": "Opportunity_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method_find", "name": "Opportunity_model::find", "doc": "&quot;Execute select query, return array of model objects. Automatically\ndiscovers table name, but can be overridden.&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method_get_entry", "name": "Opportunity_model::get_entry", "doc": "&quot;Fetch entry, automatically fetches based on table and ID&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___campaign", "name": "Opportunity_model::__campaign", "doc": "&quot;Parent campaign computed property&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___custom_fields", "name": "Opportunity_model::__custom_fields", "doc": "&quot;Custom fields computed property, returns parent campaign fields&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___donation_levels", "name": "Opportunity_model::__donation_levels", "doc": "&quot;Campaign levels computed property, returns parent campaign levels&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___header_font", "name": "Opportunity_model::__header_font", "doc": "&quot;header font computed property, returns parent campaign header font&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___campaign_color", "name": "Opportunity_model::__campaign_color", "doc": "&quot;campaign_color  computed property, returns parent campaign accent&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___enable_donation_levels", "name": "Opportunity_model::__enable_donation_levels", "doc": "&quot;has campaign levels computed property, returns parent campaign info&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___minimum_donation_amount", "name": "Opportunity_model::__minimum_donation_amount", "doc": "&quot;min donation amount computed property, returns parent campaign info&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___image_url", "name": "Opportunity_model::__image_url", "doc": "&quot;Image url computed property&quot;"},
                    {"type": "Method", "fromName": "Opportunity_model", "fromLink": "Opportunity_model.html", "link": "Opportunity_model.html#method___thumb_url", "name": "Opportunity_model::__thumb_url", "doc": "&quot;Thumbnail computed property&quot;"},
            
            {"type": "Class",  "link": "Supporter_model.html", "name": "Supporter_model", "doc": "&quot;Supporter model&quot;"},
                                                        {"type": "Method", "fromName": "Supporter_model", "fromLink": "Supporter_model.html", "link": "Supporter_model.html#method___construct", "name": "Supporter_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Supporter_model", "fromLink": "Supporter_model.html", "link": "Supporter_model.html#method___donations", "name": "Supporter_model::__donations", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Supporter_model", "fromLink": "Supporter_model.html", "link": "Supporter_model.html#method_save_entry", "name": "Supporter_model::save_entry", "doc": "&quot;Overrides parent class save&lt;em&gt;entry method by automatically generating\n * updated&lt;\/em&gt;at\n * created_at&quot;"},
            
            {"type": "Class",  "link": "Supporters.html", "name": "Supporters", "doc": "&quot;Supporters controller&quot;"},
                                                        {"type": "Method", "fromName": "Supporters", "fromLink": "Supporters.html", "link": "Supporters.html#method___construct", "name": "Supporters::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Supporters", "fromLink": "Supporters.html", "link": "Supporters.html#method_index", "name": "Supporters::index", "doc": "&quot;Index handler. Loads &#039;supporters\/index&#039; view with:&quot;"},
                    {"type": "Method", "fromName": "Supporters", "fromLink": "Supporters.html", "link": "Supporters.html#method_view", "name": "Supporters::view", "doc": "&quot;View single supporter&quot;"},
                    {"type": "Method", "fromName": "Supporters", "fromLink": "Supporters.html", "link": "Supporters.html#method_edit", "name": "Supporters::edit", "doc": "&quot;Edit supporter&quot;"},
                    {"type": "Method", "fromName": "Supporters", "fromLink": "Supporters.html", "link": "Supporters.html#method_new_supporter", "name": "Supporters::new_supporter", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Task_model.html", "name": "Task_model", "doc": "&quot;Task model&quot;"},
                                                        {"type": "Method", "fromName": "Task_model", "fromLink": "Task_model.html", "link": "Task_model.html#method_createTask", "name": "Task_model::createTask", "doc": "&quot;\n&quot;"},
            
            {"type": "Class",  "link": "Transaction_model.html", "name": "Transaction_model", "doc": "&quot;Transaction model&quot;"},
                                                        {"type": "Method", "fromName": "Transaction_model", "fromLink": "Transaction_model.html", "link": "Transaction_model.html#method___construct", "name": "Transaction_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Transaction_model", "fromLink": "Transaction_model.html", "link": "Transaction_model.html#method_for_donation", "name": "Transaction_model::for_donation", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "Transaction_model", "fromLink": "Transaction_model.html", "link": "Transaction_model.html#method_save_entry", "name": "Transaction_model::save_entry", "doc": "&quot;Overrides parent class save&lt;em&gt;entry method by automatically generating\n * permalink\n * updated&lt;\/em&gt;at\n * created_at&quot;"},
            
            {"type": "Class",  "link": "User_model.html", "name": "User_model", "doc": "&quot;User model&quot;"},
                                                        {"type": "Method", "fromName": "User_model", "fromLink": "User_model.html", "link": "User_model.html#method___construct", "name": "User_model::__construct", "doc": "&quot;\n&quot;"},
                    {"type": "Method", "fromName": "User_model", "fromLink": "User_model.html", "link": "User_model.html#method_save_entry", "name": "User_model::save_entry", "doc": "&quot;Overrides parent save_entry by setting&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


