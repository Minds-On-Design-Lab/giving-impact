<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('campaigns/_nav', array(
    'active' => false
    )) ?>
<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title"><?php echo $campaign->title ?></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>
                    Custom Donation Fields
                     <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/fields') ?>">

                    <table role="grid">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Label</th>
                                <th>Options</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="custom-fields">
                        <?php if( $campaign->custom_fields ) : foreach( $campaign->custom_fields as $field ) : ?>
                            <tr<?php echo $field->status ? '' : ' class="field-disabled"' ?>>
                                <td data-field="type" class="width10">
                                    <span><?php echo $field->field_type ?></span>
                                    <input type="hidden" name="custom_field_ids[]" value="<?php echo $field->field_id ?>" />
                                    <input type="hidden" name="custom_field_types[]" value="<?php echo $field->field_type ?>" />
                                    <input type="hidden" name="custom_field_labels[]" value="<?php echo $field->field_label ?>" />
                                    <input type="hidden" name="custom_field_options[]" value="<?php echo implode("\n", $field->options) ?>" />
                                    <input type="hidden" name="custom_field_status[]" value="<?php echo $field->status ?>" />
                                    <input type="hidden" name="custom_field_required[]" value="<?php echo $field->required ?>" />
                                </td>
                                <td data-field="label" class="width40"><?php echo $field->field_label ?></td>
                                <td data-field="options" class="width35"><?php echo $field->options ? implode("<br />", $field->options) : '' ?></td>
                                <td class="width15">
                                    <a href="#" data-action="edit-custom-field"><i class="fa fa-pencil-square-o fa-lg action"></i></a>
                                    <a href="#" data-action="hide-custom-field"><?php echo $field->status ? '<i class="fa fa-check fa-lg action"></i>' : '<i class="fa fa-times fa-lg action"></i>' ?></a></td>
                            </tr>
                        <?php endforeach; endif ?>
                        </tbody>
                    </table>

                    <a href="#" data-action="add-custom-field" class="gi-button">Add Custom Field</a>

                    <div class="row">
                        <div class="small-12 columns">
                            <hr />
                            <div class="row">
                                <div class="small-6 columns">
                                    <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                                </div>
                                <div class="small-6 columns form-submit">
                                    <input type="submit" value="Save Settings" class="gi-button" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- eo box -->
        </div>
        <div class="small-3 columns">
            &nbsp;
        </div>
    </div>
</div>

<script id="custom-field-modal" type="text/x-handlebars-template">
    <div id="customFieldModal" class="reveal-modal">
        <div class="box">
        <h2>
            New/Edit Custom Field
            <a class="right close-reveal-modal"><i class="fa fa-times-circle action"></i></a>
        </h2>

        <input type="hidden" data-field="id" value="{{id}}" />

        <label>Field Type</label>
        <select data-field="type">
            <option value="text">Single text input</option>
            <option value="dropdown">Dropdown</option>
        </select>
        <br /><br />

        <label>Field Label</label>
        <input type="text" data-field="label" value="{{label}}" />

        <div id="container-field-dropdown">
            <label>Field Options</label>
            <p class="directions">Enter one option per row.</p>
            <textarea rows="5" cols="20" data-field="options">{{options}}</textarea>
        </div>

        <label for="custom-field-required">
            <input type="checkbox" data-field="required" value="1" id="custom-field-required" />
            Required
        </label>
        <hr />

        <a href="#" class="gi-button success">Save Field</a>


        </div>
    </div>
</script>
<script id="custom-field-row" type="text/x-handlebars-template">
    <tr data-field-required="{{required}}" >
        <td data-field="type">
            <span>{{type}}</span>
            <input type="hidden" name="custom_field_ids[]" value="{{id}}" />
            <input type="hidden" name="custom_field_types[]" value="{{type}}" />
            <input type="hidden" name="custom_field_labels[]" value="{{label}}" />
            <input type="hidden" name="custom_field_options[]" value="{{options}}" />
            <input type="hidden" name="custom_field_status[]" value="1" />
            <input type="hidden" name="custom_field_required[]" value="{{required}}" />
        </td>
        <td data-field="label">{{label}}</td>
        <td data-field="options">
            {{#options_formatted}}
                {{option}}<br />
            {{/options_formatted}}
        </td>
        <td>
            <a href="#" data-action="edit-custom-field"><i class="general foundicon-edit action"></i></a>
            <a href="#" data-action="hide-custom-field"><i class="fa fa-times action"></i></a>
        </td>
    </tr>
</script>

<?php $this->load->view('components/_footer') ?>
