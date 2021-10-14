<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/public/partials
 */
?>

<?php


/**
 * Print the account registration form.
 * 
 * @param   string $authorization_url The url that redirects to the log in page.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_registration_html($authorization_url, $logged_in)
{
    if ($logged_in) {
        show_alert("You're already logged in.", false, 'notice');
    } else {
?>
        <div class="policycloud-marketplace">
            <form id="policycloud-registration" action="">
                <fieldset name="account-credentials">
                    <h2>Account credentials</h2>
                    <p>The following information is required for authorization purposes.</p>
                    <label for="username">Username *</label>
                    <input required name="username" placeholder="e.x. johndoe" type="text" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Insert your password" type="password" />
                    <label for="password-confirm">Confirm password *</label>
                    <input required name="password-confirm" placeholder="Insert your password again" type="password" />
                </fieldset>
                <fieldset name="account-details">
                    <h2>Account details</h2>
                    <p>Fill in the following fields with your personal details. This information will be used to personalize your experience within the marketplace platform and showcase your profile to other visitors. Fields marked with (*) are required for registration.</p>
                    <label for="title">Title</label>
                    <select name="title">
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="Sir">Sir</option>
                        <option value="Miss">Miss</option>
                        <option value="Mx.">Mx.</option>
                        <option value="-" selected>None</option>
                    </select>
                    <label for="name">First name *</label>
                    <input required name="name" placeholder="Insert your first name" type="text" />
                    <label for="surname">Last name *</label>
                    <input required name="surname" placeholder="Insert your last name" type="text" />
                    <label for="organization">Organization</label>
                    <input name="organization" placeholder="Insert your organization" type="text" />
                    <label for="gender">Gender</label>
                    <select name="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="transgender">Transgender</option>
                        <option value="genderqueer">Genderqueer</option>
                        <option value="questioning">Questioning</option>
                        <option value="-" selected>Prefer not to say</option>
                    </select>
                    <label for="about">Summary</label>
                    <textarea name="about" placeholder="Tell us about yourself" style="resize:vertical"></textarea>
                    <label for="socials">Related links</label>
                    <div class="socials">
                        <div>
                            <div>
                                <input type="text" name="socials-title[]" placeholder="Example" />
                                <input type="url" name="socials-url[]" placeholder="https://www.example.org/" />
                                <button class="remove-field" title="Remove this link." disabled><span class="fas fa-times"></span></button>
                            </div>
                        </div>
                        <button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
                    </div>
                </fieldset>
                <fieldset name="account-contact">
                    <h2>Account contact details</h2>
                    <p>Fill in your contact information here. This information will be used to validate your new account, as well as optionally make them available to other logged in Marketplace visitors. Fields marked with (*) are required for registration. These details remain private by default. </p>
                    <label for="email">E-mail address *</label>
                    <input type="email" name="email" placeholder="e.x. johndoe@example.org" required />
                    <label for="phone">Phone number</label>
                    <input type="tel" name="phone" placeholder="e.x. +30 6999123456" />
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action ">Create account</button>
                <p>Already have an account? Please <a href="<?php echo $authorization_url ?>">Log in</a>.</p>
            </form>
        </div>
    <?php
    }
}




/**
 * Print the account authorization form.
 * 
 * @param   string $registration_url The url that redirects to the registration page.
 * @param   bool $logged_in Whether the viewer is already logged in.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_authorization_html($registration_url, $logged_in)
{
    if (!$logged_in) {
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-authorization" action="">
                <fieldset name="account-credentials">
                    <h2>Insert your credentials</h2>
                    <p>The following information is required to log you in.</p>
                    <label for="username">Username or E-mail address *</label>
                    <input required name="username-email" placeholder="e.x. johndoe / johndoe@example.org" type="text" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Insert your password" type="password" />
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action">Log in</button>
                <p>Don't have an account yet? You can <a href="<?php echo $registration_url ?>">register</a> now to obtain full access to the Marketplace.</p>
            </form>
        </div>
    <?php
    } else {
        show_alert("You're already logged in.", false, 'notice');
    }
}

function read_multiple_html($description_objects, $args)
{
    /** 
     * TODO @elefkour: Create filter queries in form.
     * + add submit button
     * + fix views range
     * + fix date range
     * 
     * The filter names: 
     *  owner, search, title, type[], subtype, comments, contact, description, field_of_use, provider, upload_date_gte, upload_date_lte, last_updated_by, views_gte, views_lte, update_date_gte, update_date_lte
     * 
     * gte = greater than = date from / views from
     * lte = less than = date until / views up to
     * 
     */

    // TODO @elefkour: Cleanup scripts & <link>.

    if (!empty($args['error'])) {
        echo  '<div class="error-msg1"><i class="fa fa-times-circle"></i>Error message: ' . $args['error'] . '</div>';
    }
    if (empty($description_objects)) {
        echo  '<div class="error-msg1"><i class="fa fa-times-circle"></i>Error message: No description objects were found.</div>';
    } else {
    ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" type="text/css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

        <ul class="sidenav">
            <a style="pointer-events: none; cursor: default;">Filter by</a>
            <form action="" method="get">
                <input type="text" style="width:100%;" name="search" placeholder="Search..">
            </form>
            <a class="dropdown-btn1">Asset Types
                <i class="fa fa-caret-down"></i>
            </a>
            <div class="dropdown-container">
                <form id="checkbox1" action="">
                    <input type="checkbox" id="vehicle1" name="type[]" value="algorithms">
                    <label class="pccheckbox" for="vehicle1"> Algorithms </label> <br>
                    <input type="checkbox" id="vehicle2" name="type[]" value="tools">
                    <label class="pccheckbox" for="vehicle2"> Tools</label> <br>
                    <input type="checkbox" id="vehicle3" name="type[]" value="datasets">
                    <label class="pccheckbox" for="vehicle3"> Datasets </label> <br>
                    <input type="checkbox" id="vehicle4" name="type[]" value="documents">
                    <label class="pccheckbox" for="vehicle4"> Documents</label> <br>
                    <input type="checkbox" id="vehicle5" name="type[]" value="webinars">
                    <label class="pccheckbox" for="vehicle5"> Webinars</label> <br>
                    <input type="checkbox" id="vehicle6" name="type[]" value="tutorials">
                    <label class="pccheckbox" for="vehicle6"> Tutorials</label> <br>
                    <input type="checkbox" id="vehicle6" name="type[]" value="presentations">
                    <label class="pccheckbox" for="vehicle6"> Presentations</label> <br>
                    <input type="checkbox" id="vehicle6" name="type[]" value="externals">
                    <label class="pccheckbox" for="vehicle6"> Externals</label><br>
                    <input type="checkbox" id="vehicle6" name="type[]" value="other">
                    <label class="pccheckbox" for="vehicle6"> Other</label> <br>
                    <br>



            </div>
            <a class="dropdown-btn1">Filter by Owner
                <i class="fa fa-caret-down"></i>
            </a>
            <div class="dropdown-container">
                <br>

                <input type="checkbox" id="owner1" name="owner[]" value="university1">
                <label class="pccheckbox" for="owner1"> University 1</label> <br>
                <input type="checkbox" id="owner2" name="owner[]" value="university2">
                <label class="pccheckbox" for="owner2"> University 2</label> <br>
                <input type="checkbox" id="owner3" name="owner[]" value="university3">
                <label class="pccheckbox" for="owner3"> University 3</label>

                </form>

            </div>

            <a class="dropdown-btn1">Advanced search
                <i class="fa fa-caret-down"></i>
            </a>
            <div class="dropdown-container">
                <span style="color:white;">Choose a name and his value </span>
                <div class="col-1">
                    <div class="form" action="">
                        <input type="text" class="myinp" placeholder="Add Key"> <input class="myinp" type="text" placeholder="Value">
                        <button class="btn btn-primary add_field_button"><i>+</i> Add More</button>
                    </div>
                </div>
                <div class="input_fields_wrap">
                    <!-- Dynamic Fields go here -->
                </div>
            </div>

            <a style="pointer-events: none; cursor: default;"> Filter by Views</a>
            <span style="color:white;" id="slider_value"></span>
            <input type="range" style="color:white;" id="slider" value="50" min="1" max="100" step="1" />
            <input type="text" name="mindate" id="mindate" value="1">
            <br />

            <br>
            <a style="pointer-events: none; cursor: default;">Choose Dates</a>
            <div id="date-center">

                <input type="date" class="pocdate" id="datemin" name="datemin" min="2000-01-02">

                <input type="date" class="pocdate" id="datemax" name="datemax" max="1979-12-31">
                <br />
                <br />
                <input type="submit" style="background-color:white;color:black;" value="submit">

            </div>

            </div>
        </ul>

        <div class="content">
            <!-- Content -->
            <select class="pcfiltersup">
                <option>Filterby</option>
                <option>Recent</option>
                <option>Most Liked</option>
                <option>Most Views</option>
                <option>Popular</option>
            </select>

            <h1></h1>
            <section class="cards">
                <?php
                if (!empty($description_objects)) {
                    foreach ($description_objects as $description) {
                ?>
                        <!--   card 1 -->
                        <article class="card1">
                            <picture class="thumbnail1">
                                <a style="color:gray;" href="<?php echo $args['description_url'] . '?did=' . $description['id'] ?>">
                                    <img class="category__01" src="https://images.unsplash.com/photo-1541963463532-d68292c34b19?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxleHBsb3JlLWZlZWR8Mnx8fGVufDB8fHx8&w=1000&q=80" alt="" />
                            </picture>
                            <div class="card-content">
                                <p class="category1 category__01_plaisio"> <?php echo  $description['collection']; ?></p>
                                <br>

                                <span style="color:gray;font-size:12px;"> <i><img class="policy-cloud-eye-img" style="color:gray;width:15px;height:22px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/eye.svg') ?>" /> </i> 100 | <i><img class="policy-cloud-eye-img" style="color:gray;width:15px;height:12px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/calendar.svg') ?>" /></i> 2.23.2021</span>
                                <h1 class="title1"> <b><?php echo  $description['info']['title']; ?></b></h1>
                                <p class="h6"> <?php echo $description['info']['short_desc']; ?></p>
                            </div><!-- .card-content -->
                            <footer class="footer1">
                                <div class="post-meta1">

                                    <span class="views1"><span class="policy-cloud-approve-img-i" style="color:gray;font-size:12px;height:12px;"><img class="policy-cloud-approve-img" style="width:10px;height:10px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/folder.svg') ?>" /> <a style="height:8px;" href="#"> <?php echo  $description['collection']; ?></a> </span>
                                        <span class="policy-cloud-approve-img-i" style="color:gray;font-size:12px;height:12px;"><img class="policy-cloud-approve-img" style="width:10px;height:10px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/user.svg') ?>" /> <a style="height:8px;" href="#"> <?php echo  $description['info']['owner']; ?></a> </span> </span>

                                </div>
                            </footer>
                        </article>
                <?php
                    }
                } ?>
            </section>
        </div>
    <?php
    }
}

function read_single_html($description_object, $args)
{
    // TODO @elefkour: Remove comments, use PHP - check IFs for empty fields.

    //echo 'Hello ' . htmlspecialchars($_GET["did"]) . '!';
    // echo $args['description'];
    //$description[]=$args['description_object'];
    //echo ($description_object->info->collection[$_GET["did"]]);
    //$description[]=get_specific_description($_GET["did"]);

    $ownerbutton = true;
    $isuserlogin = false;;

    if (!empty($args['is_owner'])) {
        $ownerbutton = true;
    }
    if (!empty($args['authenticated'])) {
        $isuserlogin = true;
    }
    if (!empty($args['error'])) {
        echo 'Error: ' . $args['error'];
        echo  '<div class="error-msg1"><i class="fa fa-times-circle"></i>Error message' . $args['error'] . '</div>';
    }

    if (empty($description_object)) {
        echo  '<div class="error-msg1"><i class="fa fa-times-circle"></i>The description is Empty</div>';
    } else {
    ?>
        <section style="width: 1349px; left: 0px;">
            <form id="pform">
                <div class="parent">
                    <?php //only login user can see this 
                    if ($isuserlogin) { ?>
                        <div class="column">
                            <div class="tabs">
                                <ul id="tabs-nav">
                                    <li><a href="#tab1"><i class="fas fa-file-alt"></i> Files</a></li>
                                    <li><a href="#tab2"><i class="fas fa-file-download"> Gallery</i></a></li>
                                    <li><a href="#tab3"><i class="fas fa-comments"></i> Comments</a></li>
                                </ul> <!-- END tabs-nav -->
                                <div id="tabs-content">
                                    <div id="tab1" class="tab-content">
                                        <div class="accordion">Algorithm</div>
                                        <div class="panel">
                                            <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <th>Asset Name</th>
                                                        <th>Version</th>
                                                        <th>Size</th>
                                                        <th>Modified on</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <?php if (!empty($description_object)) {
                                                        foreach ($description_object['assets']['files'] as $file) {
                                                    ?>
                                                            <tr>

                                                                <td class="pctablename"><?php echo $file['filename'];
                                                                                        ?>2.0</td>
                                                                <td><?php //echo $file['version'];
                                                                    ?>2.1</td>
                                                                <td><?php //echo $file['size'];
                                                                    ?>2.2</td>
                                                                <td><?php //echo $file['updateDate'];
                                                                    ?>2.2 </td>
                                                                <td>
                                                                    <a><?php echo $file['downloads'];
                                                                        ?> <i class="fas fa-download" aria-hidden="true"></i> </a>
                                                                    &nbsp;
                                                                    <a class="edit2"><i class="fas fa-pencil-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                    <?php }
                                                    }
                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="accordion">Videos</div>
                                        <div class="panel">
                                            <br>
                                            <iframe width="420" height="345" src="https://www.youtube.com/embed/tgbNymZ7vqY">
                                            </iframe>

                                        </div>
                                    </div>
                                    <div id="tab2" class="tab-content">
                                        <h1>Images</h1>
                                        <div id="slideshow">
                                            <div class="slide-tab">
                                                <img src="https://res.cloudinary.com/trobes/image/upload/v1547224649/seattle.jpg">
                                            </div>
                                            <div class="slide-tab">
                                                <img src="https://res.cloudinary.com/trobes/image/upload/v1547224649/nightportrait.jpg">
                                            </div>
                                            <div class="slide-tab">
                                                <img src="https://res.cloudinary.com/trobes/image/upload/v1547224649/6lifeftw.jpg">
                                            </div>
                                            <div class="slide-tab">
                                                <img src="https://res.cloudinary.com/trobes/image/upload/v1547224649/mountain.jpg">
                                            </div>
                                            <div class="slide-tab">
                                                <img src="https://res.cloudinary.com/trobes/image/upload/v1547224649/bird.jpg">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab3" class="tab-content">
                                        <h2>Randall Graves</h2>
                                    </div>
                                </div> <!-- END tabs-content -->
                            </div> <!-- END tabs -->
                        </div>
                    <?php
                    } ?>
                    <div class="column">
                        <div class="main-display">
                            <h2 id="description-title" class="h2title"> <?php if (!empty($description_object['info']['title'])) {
                                                                            echo $description_object['info']['title'];
                                                                        }             ?></h2>
                            <span class="card-link" style="font-size:12px;">
                                <?php if ($isuserlogin)
                                    if (!empty($description_object['info']['owner']))
                                        echo '<i >'; ?>
                                <img class="policy-cloud-eye-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/user.svg') ?>" />
                                <?php echo '</i> ' . $description_object['info']['owner'] . '|';
                                ?> <i> <img class="policy-cloud-eye-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/eye.svg') ?>" /></i> <?php //echo $description_object['metadata']['views']; 
                                                                                                                                                                                                                                ?>

                                100 |
                                <?php if (!empty($description_object['metadata']['uploadDate'])) ?>
                                <i> <img class="policy-cloud-eye-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/calendar.svg') ?>" />

                                    <?php echo  $description_object['metadata']['uploadDate'];
                                    echo "</i>";
                                    ?>
                                    | <i class="fa fa-download">
                                        <?php //echo $description_object['metadata']['downloads']'
                                        ?> 20</i>
                                    <?php if ($ownerbutton) {
                                        if ($description_object['metadata']['approved'] == 1) { ?>
                                            <i>approved <img class="policy-cloud-eye-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/check.svg') ?>" /></i>
                                        <?php
                                        } else { ?>


                                            <i> pending<img id="policy-cloud-eye-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/pending.drawio.svg') ?>" /></i>


                                    <?php    }
                                    } ?>
                            </span>
                            <h6><b><?php if (!empty($description_object['info']['type']))
                                        print ucfirst($description_object['info']['type']);


                                    if ($description['info']['subtype'] = !"") {
                                        echo  '</b>|<b>' . $description_object['info']['subtype'] . '</b>';
                                    }
                                    ?>
                                    <br /><?php
                                            if (!empty($description_object['info']['fieldOfUse'])) {
                                                foreach ($description_object['info']['fieldOfUse'] as $fieldofuse) {
                                                    echo '</b>|<b>' . $fieldofuse . '</b>';
                                                }
                                            }
                                            ?>

                                    <b></b> </h6>
                            <a style="color:blue;font-size:15px;"> <i class="fas fa-envelope"></i> example@gmail.com</a>
                            <br>
                        </div>
                        <?php
                        if ($isuserlogin) { ?>

                            <p id="descp"><?php
                                            if (!empty($description_object['info']['description'])) {
                                                echo $description_object['info']['description'];
                                            } ?> </p>

                        <?php   } else { ?>

                            <p id="descs"><?php //echo description[info][short_desc];
                                            ?>I am text block. Click edit button to change this text. Lor
                                em ipsum dolor sit amet, consectetur adipiscing elit. Ut elit te
                                llus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
                        <?php }
                        ?>

                        <div id="pguest" class="hidden">
                            <input id="submit1" type="submit" value="Submit"> <a style="color:red;" href="#" id="pcdelete"><b>X</b> Cancel</a>
                            <br>

                        </div>
            </form>
            <?php if ($ownerbutton) { ?>
                <a href="#" id="edit1">Edit</a>
                <br>
                <?php if ($description_object['metadata']['approved'] == 1) { ?>
                    <ul id="policy-cloud-icons">
                        <li>approved</li>
                        <li> <img id="policy-cloud-approve-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/check.svg') ?>" /></li>
                    </ul>
                <?php
                } else { ?>
                    <ul id="policy-cloud-icons">
                        <li>Pending</li>
                        <li> <img id="policy-cloud-approve-img" style="width:20px;height:15px;" src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/pending.drawio.svg') ?>" /></li>
                    </ul>

            <?php    }
            } ?>
            </div>
            </div>
            </div>
        </section>
    <?php
    }
}

function object_creation_html(string $error = null)
{
    if (!empty($error)) {
        show_alert($error);
    } else {
        // TODO @alexandrosraikos: Move Fields of use outside of internal information.
        // TODO @alexandrosraikos: Comma-separated fields of use.
        // TODO @alexandrosraikos: Write subtype as a custom text field.
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-object-create" action="">
                <fieldset name="basic-information">
                    <h2>Basic information</h2>
                    <p>To create a new Marketplace asset, the following fields represent basic information that will be visible to others.</p>
                    <label for="title">Title *</label>
                    <input required name="title" placeholder="Insert a title" type="text" />
                    <label for="type">Primary collection type *</label>
                    <select name="type" required>
                        <option value="algorithms" selected>Algorithms</option>
                        <option value="tools">Tools</option>
                        <option value="policies">Policies</option>
                        <option value="datasets">Datasets</option>
                        <option value="webinars">Webinars</option>
                        <option value="tutorials">Tutorials</option>
                        <option value="documents">Documents</option>
                        <option value="externals">Externals</option>
                        <option value="other">Other</option>
                    </select>
                    <label for="subtype">Secondary collection type</label>
                    <select name="subtype">
                        <option value="" selected>-</option>
                        <option value="algorithms">Algorithms</option>
                        <option value="tools">Tools</option>
                        <option value="policies">Policies</option>
                        <option value="datasets">Datasets</option>
                        <option value="webinars">Webinars</option>
                        <option value="tutorials">Tutorials</option>
                        <option value="documents">Documents</option>
                        <option value="externals">Externals</option>
                        <option value="other">Other</option>
                    </select>
                    <label for="owner">Legal owner *</label>
                    <input required name="owner" placeholder="Insert the legal owner of the object" type="text" />
                    <label for="description">Description *</label>
                    <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"></textarea>
                </fieldset>
                <fieldset name="internal-information">
                    <h2>Internal information</h2>
                    <p>You can include internal private comments and the asset's field of use for management purposes. These fields are optional.</p>
                    <label for="field-of-use">Fields of usage</label>
                    <textarea name="field-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"></textarea>
                    <label for="comments">Comments</label>
                    <textarea name="comments" placeholder="Insert any additional comments"></textarea>
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action ">Create object</button>
            </form>
        </div>
    <?php
    }
}

/**
 * 
 * Prints an error or notice box with a close button.
 * The close button is handled @see policycloud-marketplace-public.js
 * 
 * @param string $message The message to be shown.
 * @param bool $dismissable Whether the alert is dismissable or not.
 * @param string $type The type of message, a 'notice' or an 'error'.
 * 
 * @since 1.0.0
 */
function show_alert(string $message, bool $dismissable = false, string $type = 'error')
{
    echo  '<div class="policycloud-marketplace-' . $type . ' ' . ($dismissable ? 'dismissable' : '') . '"><span>' . $message . '</span></div>';
}


/**
 * 
 * Formats a datetime string to show time passed since.
 * 
 * @param string $datetime The string depicting the date time information.
 * @param bool $full Display the full elapsed time since the specified date.
 * 
 * @since 1.0.0 
 */
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


/**
 * Display the account page HTML for authenticated users.
 *
 * @param   array $information The user information array.
 * @param   array $assets The assets connected to this account.
 * @param   array $statistics The statistics connected to this account.
 * @param   array $reviews The asset reviews connected to this account.
 * @param   array $args An array of arguments.
 * 
 * @uses    show_alert()
 * @uses    time_elapsed_string()
 * 
 * @usedby  PolicyCloud_Marketplace_Public::account_shortcode()
 * 
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_html(array $information, array $assets, array $statistics, array $reviews, array $args = [])
{
    // TODO @alexandrosraikos: Support uploading and viewing a profile picture (waiting on @vkoukos).
    // TODO @alexandrosraikos: Mockup and add reviews. (Visible by all, editable & deletable by owner, deletable by admin).
    // TODO @alexandrosraikos: Rearrange statistics labels based on instructions.

    // Check for any errors regarding authorization.
    if (!empty($args['error'])) {
        show_alert(($args['error'] == 'not-logged-in') ? 'You are not logged in, please <a href="' . $args['login_page'] . '">log in</a> to your account. Don\'t have an account yet? You can <a href="' . $args['registration_page'] . '">register</a> here.' : $args['error']);
    }

    if (!empty($information)) {

        // Check for any notices.
        if (!empty($args['notice'])) {
            show_alert($args['notice'], true, 'notice');
        }

        // Show account verification notice.
        if (!empty($information['account']['verified'])) {
            if ($information['account']['verified'] !== '1') {
                show_alert('Your account is still unverified, please check your email inbox or spam folder for a verification email. You can <a id="policycloud-marketplace-resend-verification-email">resend</a> it if you can\'t find it.', false, 'notice');
            }
        } else show_alert("Your account verification status couldn't be accessed.");
    ?>
        <div id="policycloud-account" class="policycloud-marketplace">
            <div id="policycloud-account-sidebar">
                <img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/user.svg') ?>" />
                <nav>
                    <button class="tactile" id="policycloud-account-overview" class="active">Overview</button>
                    <button class="tactile" id="policycloud-account-assets">Assets</button>
                    <button class="tactile" id="policycloud-account-reviews">Reviews</button>
                    <button class="tactile" id="policycloud-account-information">Information</button>
                    <button class="tactile policycloud-logout">Log out</button>
                </nav>
            </div>
            <div id="policycloud-account-content">
                <div class="policycloud-account-title">
                    <h2>
                        <?php
                        echo ($information['info']['title'] ?? '') . ' ' . $information['info']['name'] . ' ' . $information['info']['surname'];
                        ?>
                    </h2>
                    <div>
                        <?php
                        echo ($information['info']['organization'] ?? '');
                        ?>
                    </div>
                </div>
                <div>
                    <section class="policycloud-account-overview focused">
                        <header>
                            <h3>Overview</h3>
                        </header>
                        <div>
                            <h4>About</h4>
                            <p>
                                <?php echo $information['info']['about'] ?? '' ?>
                            </p>
                            <?php
                            if (!empty($information['info']['social'])) {
                            ?>
                                <ul>
                                    <?php
                                    foreach ($information['info']['social'] as $link) {
                                        echo '<li><a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                        </div>
                        <?php if (!empty($statistics)) { ?>
                            <h4>Statistics</h4>
                            <table class="statistics">
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-check"></span> <?php echo $statistics['approved_descriptions'] ?></div>
                                        <div class="assets-caption">Approved descriptions</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-file"></span> <?php echo $statistics['assets_uploaded'] ?></div>
                                        <div>Assets uploaded</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-star"></span> <?php echo $statistics['average_rating'] ?></div>
                                        <div class="assets-caption">Average rating</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-list"></span> <?php echo $statistics['total_descriptions'] ?></div>
                                        <div class="assets-caption">Total descriptions</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-download"></span> <?php echo $statistics['total_downloads'] ?></div>
                                        <div class="assets-caption">Total downloads</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-comment"></span> <?php echo $statistics['total_reviews'] ?></div>
                                        <div class="assets-caption">Total reviews</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-eye"></span> <?php echo $statistics['total_views'] ?></div>
                                        <div class="assets-caption">Total views</div>
                                    </td>
                                </tr>
                            </table>
                        <?php
                        } else {
                            show_alert("Statistics for this user are currently unavailable.", false, 'notice');
                        }
                        ?>
                    </section>
                    <section class="policycloud-account-assets">
                        <header>
                            <h3>Assets</h3>
                            <div class="actions">
                                <form action="" class="selector">
                                    <label for="sort-by">Sort by</label>
                                    <select name="sort-by">
                                        <option value="newest" <?php echo ((($_GET['sort_by'] ?? '' == 'newest') || empty($_GET['sort_by'])) ? "selected" : "") ?>>Newest</option>
                                        <option value="oldest" <?php echo (($_GET['sort_by'] ?? '' == 'oldest') ? "selected" : "") ?>>Oldest</option>
                                        <option value="rating-asc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-asc') ? "selected" : "") ?>>Highest rated</option>
                                        <option value="rating-desc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-desc') ? "selected" : "") ?>>Lowest rated</option>
                                        <option value="views-asc" <?php echo (($_GET['sort_by'] ?? '' == 'views-asc') ? "selected" : "") ?>>Most viewed</option>
                                        <option value="views-desc" <?php echo (($_GET['sort_by'] ?? '' == 'views-desc') ? "selected" : "") ?>>Least viewed</option>
                                        <option value="title" <?php echo (($_GET['sort_by'] ?? '' == 'title') ? "selected" : "") ?>>Title</option>
                                    </select>
                                    <label for="items-per-page">Items per page</label>
                                    <select name="items-per-page">
                                        <option value="5" <?php echo ((($_GET['items_per_page'] ?? '' == '5') || empty($_GET['items_per_page'])) ? "selected" : "") ?>>5</option>
                                        <option value="10" <?php echo (($_GET['items_per_page'] ?? '' == '10') ? "selected" : "") ?>>10</option>
                                        <option value="25" <?php echo (($_GET['items_per_page'] ?? '' == '25') ? "selected" : "") ?>>25</option>
                                        <option value="50" <?php echo (($_GET['items_per_page'] ?? '' == '50') ? "selected" : "") ?>>50</option>
                                        <option value="100" <?php echo (($_GET['items_per_page'] ?? '' == '100') ? "selected" : "") ?>>100</option>
                                    </select>
                                </form>
                                <?php
                                if (!$args['visiting']) {
                                ?>
                                    <a id="policycloud-upload" href="<?php echo $args['upload_page'] ?>" title="Create a new asset"><span class="fas fa-plus"></span> Create new asset</a>
                                <?php } ?>
                            </div>
                        </header>
                        <div id="policycloud-account-asset-collection-filters">
                            <div>Filter by type:</div>
                        </div>
                        <div id="policycloud-account-assets-list">
                            <?php
                            if (!empty($assets['results'])) {
                                foreach ($assets['results'] as $page => $grouped_assets) {
                            ?>
                                    <ul data-page="<?php echo $page + 1 ?>" class="<?php echo ($page == ($_GET['page'] ?? 0)) ? 'visible' : '' ?>">
                                        <?php
                                        if (!empty($assets)) {
                                            foreach ($grouped_assets as $asset) {
                                        ?>
                                                <li data-type-filter="<?php echo $asset['info']['type'] ?>" data-date-updated="<?php echo strtotime($asset['metadata']['uploadDate']) ?>" data-average-rating="<?php echo $asset['metadata']['reviews']['average_rating'] ?>" data-total-views="<?php echo $asset['metadata']['views'] ?>" class="visible">
                                                    <div class="description">
                                                        <a href="<?php echo $args['description_page'] . "?did=" . $asset['id'] ?>">
                                                            <h4><?php echo $asset['info']['title'] ?></h4>
                                                        </a>
                                                        <p><?php echo $asset['info']['short_desc'] ?></p>
                                                        <div class="metadata">
                                                            <a class="pill"><?php echo $asset['info']['type']  ?></a>
                                                            <a class="pill"><?php echo $asset['info']['subtype']  ?></a>
                                                            <span><span class="fas fa-star"></span> <?php echo $asset['metadata']['reviews']['average_rating'] . ' (' . $asset['metadata']['reviews']['no_reviews'] . ' reviews)' ?></span>
                                                            <span><span class="fas fa-eye"></span> <?php echo $asset['metadata']['views'] ?> views</span>
                                                            <span>Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset['metadata']['uploadDate']))) ?></span>
                                                            <span class="label <?php echo ($asset['metadata']['approved'] == 1) ? 'success' : 'notice' ?>"><?php echo ($asset['metadata']['approved'] == 1) ? 'Approved' : 'Pending' ?></span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <p class="policycloud-account-notice">Upload your first asset to get started.</p>
                                        <?php } ?>
                                    </ul>
                            <?php
                                }
                            } else {
                                show_alert('This user has not uploaded any assets yet.', false, 'notice');
                            } ?>
                            <nav class="pagination">
                                <?php
                                if (count($assets['results'] ?? []) > 1) {
                                    foreach ($assets['results'] as $page => $grouped_assets) {
                                        echo '<button class="page-selector ' . (($page == ($_GET['page'] ?? 0)) ? 'active' : '') . '" data-assets-page="' . $page + 1 . '">' . ($page + 1) . '</button>';
                                    }
                                } ?>
                            </nav>
                        </div>
                    </section>
                    <section class="policycloud-account-reviews">
                        <header>
                            <h3>Reviews</h3>
                        </header>
                        <p>Coming soon!</p>
                    </section>
                    <section class="policycloud-account-information">
                        <header>
                            <h3>Information</h3>
                            <?php
                            if (!$args['visiting'] || $args['is_admin']) {
                            ?>
                                <button id="policycloud-marketplace-account-edit-toggle"><span class="fas fa-pen"></span> Edit</button>
                            <?php
                            }
                            ?>
                        </header>
                        <form id="policycloud-marketplace-account-edit" action="">
                            <table class="information">
                                <tr>
                                    <td>
                                        Summary
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php echo $information['info']['about']; ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <textarea name="about" class="folding" placeholder="Tell us about yourself" style="resize:vertical"><?php echo $information['info']['about'] ?? ''; ?></textarea>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Related links
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            foreach ($information['info']['social'] as $link) {
                                                echo '<a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a><br/>';
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <div class="socials folding">
                                                <div>
                                                    <?php
                                                    foreach ($information['info']['social'] as $key => $link) {
                                                        $link_title = explode(':', $link, 2)[0];
                                                        $link_url = explode(':', $link, 2)[1];
                                                    ?>
                                                        <div>
                                                            <input type="text" name="socials-title[]" placeholder="Example" value="<?php echo $link_title ?>" />
                                                            <input type="url" name="socials-url[]" placeholder="https://www.example.org/" value="<?php echo $link_url ?>" />
                                                            <button class="remove-field" title="Remove this link." <?php if (count($information['info']['social']) == 1) echo 'disabled' ?>><span class="fas fa-times"></span></button>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Username
                                    </td>
                                    <td>
                                        <span>
                                            <?php
                                            echo ($information['username'] ?? '-');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    if (!$args['visiting']) {
                                    ?>
                                        <td>
                                            Password
                                        </td>
                                        <td>
                                            <span class="folding visible">*****************</span>
                                            <input class="folding" type="password" name="password" placeholder="Enter your new password here" />
                                            <input class="folding" type="password" name="password-confirm" placeholder="Confirm new password here" />
                                        <?php
                                    }
                                        ?>
                                        </td>
                                </tr>
                                <tr>
                                    <td>
                                        Role
                                    </td>
                                    <td>
                                        <span>
                                            <?php echo ($information['account']['role'] == 'admin') ? 'Administrator' : 'User'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Full name
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($information['info']['title'] ?? '') . ' ' . ($information['info']['name']) . ' ' . ($information['info']['surname']);
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <select class="folding" name="title">
                                                <option value="Mr." <?php echo ($information['info']['title'] == 'Mr.' ? 'selected' : '') ?>>Mr.</option>
                                                <option value="Ms." <?php echo ($information['info']['title'] == 'Ms.' ? 'selected' : '') ?>>Ms.</option>
                                                <option value="Mrs." <?php echo ($information['info']['title'] == 'Mrs.' ? 'selected' : '') ?>>Mrs.</option>
                                                <option value="Dr." <?php echo ($information['info']['title'] == 'Dr.' ? 'selected' : '') ?>>Dr.</option>
                                                <option value="Prof." <?php echo ($information['info']['title'] == 'Prof.' ? 'selected' : '') ?>>Prof.</option>
                                                <option value="Sir" <?php echo ($information['info']['title'] == 'Sir' ? 'selected' : '') ?>>Sir</option>
                                                <option value="Miss" <?php echo ($information['info']['title'] == 'Miss' ? 'selected' : '') ?>>Miss</option>
                                                <option value="Mx." <?php echo ($information['info']['title'] == 'Mx.' ? 'selected' : '') ?>>Mx.</option>
                                                <option value="-" <?php echo ($information['info']['title'] == '-' ? 'selected' : '') ?>>None</option>
                                            </select>
                                            <input class="folding" type="text" name="name" placeholder="Name" value="<?php echo ($information['info']['name']); ?>" required />
                                            <input class="folding" type="text" name="surname" placeholder="Surname" value="<?php echo ($information['info']['surname']); ?>" required />
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Gender
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($information['info']['gender'] ?? '-');
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <select name="gender" class="folding">
                                                <option value="male" <?php echo ($information['info']['gender'] == 'male' ? 'selected' : '') ?>>Male</option>
                                                <option value="female" <?php echo ($information['info']['gender'] == 'female' ? 'selected' : '') ?>>Female</option>
                                                <option value="transgender" <?php echo ($information['info']['gender'] == 'transgender' ? 'selected' : '') ?>>Transgender</option>
                                                <option value="genderqueer" <?php echo ($information['info']['gender'] == 'genderqueer' ? 'selected' : '') ?>>Genderqueer</option>
                                                <option value="questioning" <?php echo ($information['info']['gender'] == 'questioning' ? 'selected' : '') ?>>Questioning</option>
                                                <option value="-" <?php echo ($information['info']['gender'] == '-' ? 'selected' : '') ?>>Prefer not to say</option>
                                            </select>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Organization
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($information['info']['organization'] ?? '-');
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <input class="folding" type="text" name="organization" value="<?php echo ($information['info']['organization'] ?? ''); ?>" placeholder="Insert your organization here" />
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($information['info']['email']) || !$args['visiting']) {
                                ?>
                                    <tr>
                                        <td>
                                            E-mail
                                        </td>
                                        <td>
                                            <span class="folding visible">
                                                <?php
                                                echo ($information['info']['email']);
                                                if ($information['account']['verified'] != '1') {
                                                ?>
                                                    <span class="unverified">(Unverified)</span>
                                                    <button id="policycloud-marketplace-resend-verification-email">Resend verification email</button>
                                                <?php
                                                } else {
                                                    if (!$args['visiting'] || $args['is_admin']) {
                                                        echo ' <span class="label ' . (($information['profile_parameters']['public_email'] == 0) ? 'notice' : 'success') . '">' . (($information['profile_parameters']['public_email'] == 0) ? 'Private' : 'Public') . '</span>';
                                                    }
                                                }
                                                ?>
                                            </span>
                                            <?php
                                            if (!$args['visiting'] || $args['is_admin']) {
                                            ?>
                                                <label for="email" class="folding">Changing this setting will require a verification of the new e-mail address.</label>
                                                <input class="folding" type="email" name="email" value="<?php echo $information['info']['email'] ?>" required />
                                                <select name="public-email" class="folding">
                                                    <option value="1" <?php echo ($information['profile_parameters']['public_email'] == 1 ? 'selected' : '') ?>>Public</option>
                                                    <option value="0" <?php echo ($information['profile_parameters']['public_email'] == 0 ? 'selected' : '') ?>>Private</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                if (!empty($information['info']['phone']) || !$args['visiting']) {
                                ?>
                                    <tr>
                                        <td>
                                            Phone number
                                        </td>
                                        <td>
                                            <span class="folding visible">
                                                <?php
                                                if (!empty($information['info']['phone'])) {
                                                    echo ($information['info']['phone']);
                                                    if (!$args['visiting'] || $args['is_admin']) {
                                                        echo ' <span class="label ' . (($information['profile_parameters']['public_phone'] == 0) ? 'notice' : 'success') . '">' . (($information['profile_parameters']['public_phone'] == 0) ? 'Private' : ' Public') . '</span>';
                                                    }
                                                } else echo '-';
                                                ?>
                                            </span>
                                            <?php
                                            if (!$args['visiting'] || $args['is_admin']) {
                                            ?>
                                                <input class="folding" type="text" name="phone" value="<?php
                                                                                                        echo (empty($information['info']['phone']) ? '' : $information['info']['phone']); ?>" placeholder="Insert your phone number here" />
                                                <select name="public-phone" class="folding">
                                                    <option value="1" <?php echo ($information['profile_parameters']['public_phone'] == 1 ? 'selected' : '') ?>>Public</option>
                                                    <option value="0" <?php echo ($information['profile_parameters']['public_phone'] == 0 ? 'selected' : '') ?>>Private</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td>
                                        Member since
                                    </td>
                                    <td>
                                        <?php
                                        echo date('d/m/y', strtotime($information['account']['registration_datetime']))
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                            if (!$args['visiting'] || $args['is_admin']) {
                            ?>
                                <div class="folding error"></div>
                                <div class="folding notice"></div>
                                <div class="critical-action">
                                    <label for="current-password">Please type your current password to continue.</label>
                                    <input name="current-password" type="password" placeholder="Insert your current password here">
                                </div>
                                <button type="submit" class="folding">Submit</button>
                            <?php
                            }
                            ?>
                        </form>
                        <?php
                        if (!$args['visiting']) {
                        ?>
                            <button id="policycloud-marketplace-request-data-copy" class="action">Request data copy</button>
                        <?php
                        }
                        if (!$args['visiting'] || $args['is_admin']) {
                        ?>
                            <form id="policycloud-marketplace-delete-account">
                                <div>
                                    <label for="current-password">Please type your current password to continue.</label>
                                    <input name="current-password" type="password" placeholder="Insert your current password here">
                                </div>
                                <button type="submit" class="action destructive">Delete account</button>
                            </form>
                        <?php } ?>
                    </section>
                </div>
            </div>
        </div>
<?php
    }
}
