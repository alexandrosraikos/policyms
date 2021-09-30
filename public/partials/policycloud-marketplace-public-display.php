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

function registration_form_html()
{
?>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">

    <!------ Include the above in your HEAD tag ---------->
    <div class="container">
        <div class="card bg-light">
            <article class="card-body mx-auto" style="max-width: 400px;">
                <h4 class="card-title mt-3 text-center">Create Account</h4>
                <p class="text-center">Get started with your free account</p>
                <form id="policycloud-registration" action="">
                    <div class="form-row">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                            </div>
                            <input required name="username" class="form-control" placeholder="username" id="username" type="text">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                            </div>
                            <input required name="name" class="form-control" placeholder="name" id="name" type="text">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                            </div>
                            <input required name="surname" class="form-control" placeholder="surname" type="text">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                            </div>
                            <input required name="email" class="form-control" placeholder="Email address" type="email">
                        </div> <!-- form-group// -->
                    </div>
                    <div class="form-row">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                            </div>

                            <input required name="phone" class="form-control" placeholder="phone" type="text">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-building"></i> </span>
                            </div>

                            <input required name="organization" class="form-control" placeholder="organization" type="text">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-building"></i> </span>
                            </div>
                            <select required name="title" class="form-control">
                                <option selected="">Title</option>
                                <option>Mr</option>
                                <option>Mrs</option>
                                <option>Doc</option>
                                <option>Prof</option>
                            </select>
                        </div> <!-- form-group end.// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fas fa-genderless"></i> </span>
                            </div>
                            <select required name="gender" class="form-control">
                                <option selected="">Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                            </div>
                            <input required name="password" class="form-control" placeholder="Create password" type="password">
                        </div> <!-- form-group// -->
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                            </div>
                            <input required name="password-confirm" class="form-control" placeholder="Repeat password" type="password">
                        </div> <!-- form-group// -->
                        <div class="form-group">

                            <button type="submit" class="btn btn-primary btn-block submit-registration"> Create Account </button>
                        </div> <!-- form-group// -->
                        <div class="registration-error">

                        </div>
                    </div>
                    <p class="text-center">Have an account? <a href="">Log In</a> </p>
                </form>
            </article>
        </div> <!-- card.// -->

    </div>

<?php
}


function login_form_html()
{

?>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">

    <!------ Include the above in your HEAD tag ---------->
    <div class="container">
        <div class="card bg-light">
            <article class="card-body mx-auto" style="max-width: 400px;">
                <h4 class="card-title mt-3 text-center">Log In</h4>
                <form id="policycloud-login" action="">
                    <div class="form-row">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                            </div>
                            <input required name="policycloud-marketplace-username" class="form-control" placeholder="Username" id="username" type="text">
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                            </div>
                            <input required name="policycloud-marketplace-password" class="form-control" placeholder="Password" type="password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"> Log In </button>
                    </div> <!-- form-group// -->
                    <div class="login-error">
                        <p class="text-center">
                            <a href="">Forgot password</a>
                        </p>
                    </div>
        </div>
        </form>
        </article>
    </div>
    </div>

    <?php
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
            <a>Filter by</a>
            <form action="" method="get">
                <input type="text" style="width:100%;" name="search" placeholder="Search..">
            </form>
            <button class="dropdown-btn1">Asset Types
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <form id="checkbox1" action="">
                    <input type="checkbox" id="vehicle1" name="type" value="algorithms">
                    <label class="pccheckbox" for="vehicle1"> Algorithms </label> <br>
                    <input type="checkbox" id="vehicle2" name="type" value="tools">
                    <label class="pccheckbox" for="vehicle2"> Tools</label> <br>
                    <input type="checkbox" id="vehicle3" name="type" value="datasets">
                    <label class="pccheckbox" for="vehicle3"> Datasets </label> <br>
                    <input type="checkbox" id="vehicle4" name="type" value="outcomes">
                    <label class="pccheckbox" for="vehicle4"> Project's Outcomes</label> <br>
                    <input type="checkbox" id="vehicle5" name="type" value="webinars">
                    <label class="pccheckbox" for="vehicle5"> Webinars</label> <br>
                    <input type="checkbox" id="vehicle6" name="type" value="tutorials">
                    <label class="pccheckbox" for="vehicle6"> tutorias</label> <br>



            </div>
            <button class="dropdown-btn1">Filter by Owner
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <br>

                <input type="checkbox" id="owner1" name="owner" value="university1">
                <label class="pccheckbox" for="owner1"> University 1</label> <br>
                <input type="checkbox" id="owner2" name="owner" value="university2">
                <label class="pccheckbox" for="owner2"> University 2</label> <br>
                <input type="checkbox" id="owner3" name="owner" value="university3">
                <label class="pccheckbox" for="owner3"> University 3</label>

                </form>

            </div>

            <button class="dropdown-btn1">Advanced search
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-container">
                <div class="col-1">
                    <div class="form" action="">
                        <input type="text" class="myinp" placeholder="Add Key"> <input class="myinp" type="text" placeholder="Value">
                        <button class="btn btn-primary add_field_button"><i class="fa fa-plus"></i> Add More</button>
                    </div>
                </div>
                <div class="input_fields_wrap">
                    <!-- Dynamic Fields go here -->
                </div>
            </div>
            <br>
            <a>Filter by Views</a>
            <div class="range-wrap">
                <div class="range-value" id="rangeV"></div>
                <input id="range" type="range" min="200" max="800" value="200" step="1">
            </div>
            <script>
                const
                    range = document.getElementById('range'),
                    rangeV = document.getElementById('rangeV'),
                    setValue = () => {
                        const
                            newValue = Number((range.value - range.min) * 100 / (range.max - range.min)),
                            newPosition = 10 - (newValue * 0.2);
                        rangeV.innerHTML = `<span>${range.value}</span>`;
                        rangeV.style.left = `calc(${newValue}% + (${newPosition}px))`;
                    };
                document.addEventListener("DOMContentLoaded", setValue);
                range.addEventListener('input', setValue);
            </script>
            <br>

            <a>Choose Dates</a> <input type="date" class="pocdate" id="datemin" name="datemin" min="2000-01-02">

            <input type="date" class="pocdate" id="datemax" name="datemax" max="1979-12-31">
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
                                <span style="color:gray;font-size:12px;"> <i class="far fa-eye"></i> 100 | <i class="far fa-calendar-alt"></i> 2.23.2021</span>
                                <h1 class="title1"> <b><?php echo  $description['info']['title']; ?></b></h1>
                                <p class="h6"> <?php echo $description['info']['short_desc']; ?></p>
                            </div><!-- .card-content -->
                            <footer class="footer1">
                                <div class="post-meta1">
                                    <span class="views1"><i class="fas fa-folder-open"></i> <a href="#"> <?php echo  $description['collection']; ?></a></span>
                                    <span class="views1"><i class="fas fa-user"></i> <a href="#"> University of Nicosia</a></span>
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
    // TODO @elefkour: Show $description_object['metadata']['approved'] if $is_owner.
    // TODO @elefkour: Remove comments, use PHP.

    $ownerbutton = true;
    $isuserlogin = true;

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
                                                    <?php //foreach ($description['assets']['files'] as $file) { 
                                                    ?>
                                                    <tr>

                                                        <td id="pctablename"><?php // echo $file['filename']; 
                                                                                ?>2.0</td>
                                                        <td><?php //echo $file['version'];
                                                            ?>2.1</td>
                                                        <td><?php //echo $file['size'];
                                                            ?>2.2</td>
                                                        <td><?php //echo $file['updateDate'];
                                                            ?>2.2 </td>
                                                        <td>
                                                            <a><?php //echo $file['download'];
                                                                ?><i class="fas fa-download" aria-hidden="true"></i> </a>
                                                            &nbsp;
                                                            <a class="edit2"><i class="fas fa-pencil-alt"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php //}
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
                            <h2 id="description-title" class="h2title"> <?php //echo $description_object['info']['title'];
                                                                        ?>Deep Learning</h2>
                            <span class="card-link" style="color:gray;font-size:12px;">
                                <?php //if ($isuserlogin) echo '<i class="far fa-user"></i> University of Nicosia '.$descritption[info][owner].'|';
                                ?> <i class="far fa-eye"></i> <?php //echo $description_object['metadata']['views']; 
                                                                ?>
                                100 | <i class="far fa-calendar-alt"></i>
                                <?php // echo $description_object['metadata']['updateDate'];
                                ?>
                                2.23.2021 | <i class="fa fa-download">
                                    <?php //echo $description_object['metadata']['downloads']'
                                    ?> 20</i></span>
                            <h6><b>Algorithms <?php //echo $description_object['collection'];
                                                ?>
                                    <?php if (!empty($description_object['info']['fieldOfUse'])) {
                                        echo  '</b>|<b>' . $description_object['info']['fieldOfUse'] . '</b>';
                                    }
                                    if (!empty($description['info']['subtype'])) {
                                        echo  '</b>|<b>' . $description_object['info']['subtype'] . '</b>';
                                    } ?>
                                    <b></b> </h6>
                            <a style="color:blue;font-size:15px;"> <i class="fas fa-envelope"></i> example@gmail.com</a>
                            <br>
                        </div>
                        <?php
                        if ($isuserlogin) {
                            if ($ownerbutton) { ?>
                                <p id="descp"><?php //echo description_object[info][description];
                                                ?>
                                    I am text block. Click edit button to change this text. Lor
                                    em ipsum dolor sit amet, consectetur adipiscing elit. Ut elit te
                                    llus, luctus nec ullamcorper mattis, pulvinar dapibus leo</p>
                            <?php
                            } else { ?>

                                <p id="descp"><?php //echo description[info][description]; 
                                                ?> I am text block. Click edit button to change this text. Lor
                                    em ipsum dolor sit amet, consectetur adipiscing elit. Ut elit te
                                    llus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
                            <?php  }
                        } else { ?>

                            <p id="descs"><?php //echo description[info][short_desc];
                                            ?>I am text block. Click edit button to change this text. Lor
                                em ipsum dolor sit amet, consectetur adipiscing elit. Ut elit te
                                llus, luctus nec ullamcorper mattis, pulvinar dapibus leo.</p>
                        <?php }
                        ?>

                        <div id="pguest" class="hidden">
                            <input id="submit1" type="submit" value="Submit">
                            <br>
                        </div>
            </form>
            <?php if ($ownerbutton) { ?>
                <button id="edit1">Edit</button>
                <br>
                <?php if ($description_object['metadata']['approved'] == 1) { ?>
                    <img id="pcapproved" src="http://localhost/marketplace/approved.jpg" style="width:100px;height:50px;">
                <?php
                } else { ?>
                    <img id="pcpending" src="http://localhost/marketplace/pending.jpg" style="width:100px;height:50px;">
            <?php    }
            } ?>
            </div>
            </div>
            </div>
        </section>
    <?php
    }
}


function create_object_html(string $error = null)
{
    // TODO @elefkour: Fix icons.

    if (empty($args['authenticated'])) {
        echo '<div class="error-msg1"><i class="fa fa-times-circle"></i>You have to login first.</div>';
    } else {
    ?>
        <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-11 col-sm-10 col-md-10 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <form id="msform">
                            <h2 id="heading">Upload your Project</h2>
                            <p>Fill all form field to go to next step</p>
                            <!-- progressbar -->
                            <ul id="progressbar">
                                <li class="active" id="account"><strong>Asset name,category and description </strong></li>
                                <li id="personal"><strong>Field of use and Author Comment</strong></li>
                                <li id="payment"><strong>Images and Files</strong></li>
                                <li id="confirm"><strong>Finish</strong></li>
                            </ul>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                            </div> <br> <!-- fieldsets -->
                            <fieldset>
                                <div class="form-card">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="fs-title">Asset name,category and description:</h2>
                                        </div>
                                        <div class="col-5">
                                            <h2 class="steps">Step 1 - 4</h2>
                                        </div>
                                    </div> <label class="fieldlabels">Asset Name: *</label> <input type="text" name="aname" placeholder="Asset name" /> <label class="fieldlabels"></label> <input id='usernameid' type="hidden" value='12345' /><label class="fieldlabels">Category: *</label> <input type="text" name="acat" placeholder="Category" /> <label class="fieldlabels">Owner: *</label> <input type="owner" name="owner" placeholder="Owner" /> <label class="fieldlabels">Description: *</label><textarea name="editor1"></textarea>
                                </div> <input type="button" name="next" class="next action-button" value="Next" />
                            </fieldset>
                            <fieldset>
                                <div class="form-card">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="fs-title">Asset private Information:</h2>
                                        </div>
                                        <div class="col-5">
                                            <h2 class="steps">Step 2 - 4</h2>
                                        </div>
                                    </div> <label class="fieldlabels">Field of Use: *</label> <input type="text" name="fieldOfUse" placeholder="Key Words" /> <label class="fieldlabels">Creator Comments</label><textarea name="comment"></textarea>
                                    <script>
                                        CKEDITOR.replace('comment');
                                    </script>
                                </div> <input type="button" name="next" class="next action-button" value="Next" /> <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                            </fieldset>
                            <fieldset>
                                <div class="form-card">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="fs-title">Files Upload:</h2>
                                        </div>
                                        <div class="col-5">
                                            <h2 class="steps">Step 3 - 4</h2>
                                        </div>
                                    </div> <label class="fieldlabels">Upload Your Photo:</label> <input type="file" name="pic" accept="image/*"> <label class="fieldlabels">Upload Signature Photo:</label> <input type="file" name="pic" accept="image/*">
                                    <label class="fieldlabels">Upload your Files:</label> <input type="file" name="file_upl" id="file_upl">
                                    <label class="fieldlabels"><b>Or</b> create your file:</label><textarea name="code"></textarea>
                                    <script>
                                        CKEDITOR.replace('code');
                                    </script>
                                </div> <input type="button" name="next" class="next action-button" value="Submit" /> <input type="button" name="previous" class="previous action-button-previous" value="Previous" />
                            </fieldset>
                            <fieldset>
                                <div class="form-card">
                                    <div class="row">
                                        <div class="col-7">
                                            <h2 class="fs-title">Finish:</h2>
                                        </div>
                                        <div class="col-5">
                                            <h2 class="steps">Step 4 - 4</h2>
                                        </div>
                                    </div> <br><br>
                                    <h2 class="purple-text text-center"><strong>SUCCESS !</strong></h2> <br>
                                    <div class="row justify-content-center">
                                        <div class="col-3"> <img src="https://i.imgur.com/GwStPmg.png" class="fit-image"> </div>
                                    </div> <br><br>
                                    <div class="row justify-content-center">
                                        <div class="col-7 text-center">
                                            <h5 class="purple-text text-center">You Have Successfully Uploaded Your Asset</h5>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
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
function show_alert(string $message, bool $dismissable = false, string $type = 'error') {
    echo  '<div class="policycloud-marketplace-'.$type.' '.($dismissable ? 'dismissable' : '').'"><span>'.$message.'</span></div>';
}


/**
 * Display the account page HTML for authenticated users.
 *
 * @param   $token The decoded user token.
 * @param   array $descriptions The relevant Descriptions from the PolicyCloud Marketplace API.
 * @param   array $args An array of arguments.
 * 
 * @uses    show_alert()
 * @since   1.0.0
 */
function user_account_html($token, array $descriptions = null, array $args)
{
    if (empty($token)) {
        if (!empty($args['login_page']) || !empty($args['registration_page'])) {
            show_alert('You are not logged in, please <a href="' . $args['login_page'] . '">log in</a> to your account. Don\'t have an account yet? You can <a href="' . $args['registration_page'] . '">register</a> here.');
        } else {
            show_alert('An error occured: ' . $args['error']);
        }
    } else {
        if(!empty($args['error'])) {
            show_alert($args['error']);
        }
        if(!empty($args['notice'])) {
            show_alert($args['notice'], true, 'notice');
        }
        if($token->account->verified !== '1') {
            show_alert('Your account is still unverified, please check your email inbox or spam folder for a verification email. You can <a id="policycloud-marketplace-resend-verification-email">resend</a> it if you can\'t find it.');
        }
    ?>
        <div id="policycloud-account">
            <div id="policycloud-account-sidebar">
                <img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/user.svg') ?>" />
                <!-- This is displayed only in the mobile version -->
                <div class="policycloud-account-title">
                    <h2>
                        <?php
                        echo ($token->info->title ?? '') . ' ' . ($token->info->name ?? '') . ' ' . ($token->info->surname ?? '');
                        ?>
                    </h2>
                    <div>
                        <?php
                        echo ($token->info->organization ?? '');
                        ?>
                    </div>
                </div>
                <!--------------------------------------------------->
                <div id="policycloud-account-hyperlinks">
                    <?php
                    if ($token->profile_parameters->public_email && !empty($token->info->email)) {
                    ?>
                        <a title="Send an email" href="mailto:<?php echo sanitize_email($token->info->email ?? '') ?>"><img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/email.svg') ?>" /></a>
                    <?php
                    }
                    if (!empty($token->info->webpage)) {
                    ?>
                        <a title="Visit the official webpage" href="<?php echo esc_url($token->info->webpage ?? '') ?>"><img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/globe.svg') ?>" /></a>
                    <?php
                    }
                    if ($token->profile_parameters->public_phone && !empty($token->info->phone)) {
                    ?>
                        <a title="Call" href="tel:<?php echo ($token->info->phone ?? '') ?>"><img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/phone.svg') ?>" /></a>
                    <?php
                    }
                    ?>
                </div>
                <nav>
                    <button id="policycloud-account-overview" class="active">Overview</button>
                    <button id="policycloud-account-assets">Assets</button>
                    <button id="policycloud-account-likes">Likes</button>
                    <button id="policycloud-account-details">Account Details</button>
                    <button class="policycloud-logout">Log Out</button>
                </nav>
            </div>
            <div id="policycloud-account-content">
                <!-- This is displayed only in the desktop version -->
                <div class="policycloud-account-title">
                    <h2>
                        <?php
                        echo ($token->info->title ?? '') . ' ' . ($token->info->name ?? '') . ' ' . ($token->info->surname ?? '');
                        ?>
                    </h2>
                    <div>
                        <?php
                        echo ($token->info->organization ?? '');
                        ?>
                    </div>
                </div>
                <!--------------------------------------------------->
                <div>
                    <section class="policycloud-account-overview focused">
                        <header>
                            <h3>Overview</h3>
                        </header>
                        <table class="statistics">
                            <tr>
                                <td>
                                    <div class="large-figure"><?php echo count($descriptions ?? []) ?></div>
                                    <div class="assets-caption">Assets uploaded</div>
                                </td>
                                <td>
                                    <div class="large-figure"><?php echo '0' ?></div>
                                    <div>Likes</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="large-figure">
                                        <?php
                                        echo array_sum(array_map(function ($description) {
                                            return $description['metadata']['views'] ?? 0;
                                        }, $descriptions ?? []));
                                        ?>
                                    </div>
                                    <div>Total views</div>
                                </td>
                                <td>
                                    <div class="large-figure">
                                        <?php
                                        echo array_sum(array_map(function ($description) {
                                            return $description['metadata']['downloads'] ?? 0;
                                        }, $descriptions ?? []));
                                        ?>
                                    </div>
                                    <div>Total downloads</div>
                                </td>
                            </tr>
                        </table>
                    </section>
                    <section class="policycloud-account-assets">
                        <header>
                            <h3>Assets</h3>
                            <a id="policycloud-upload" href="" title="Create a new asset"><img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/plus.svg') ?>" />Create new asset</a>
                        </header>
                        <div id="policycloud-account-asset-collection-filters">
                            <?php
                            $collections = array_unique(array_map(function ($description) {
                                return $description['info']['type'];
                            }, $descriptions ?? []));
                            foreach ($collections as $collection) {
                            ?>
                                <button data-type-filter="<?php echo $collection ?>"><?php echo $collection ?></button>
                            <?php
                            }
                            ?>
                        </div>
                        <ul id="policycloud-account-assets-list">
                            <?php
                            if (!empty($descriptions)) {
                                foreach ($descriptions as $description) {
                            ?>
                                    <li class="<?php echo $description['info']['type'] ?> visible">
                                        <a href="<?php echo $args['description_page'] . "?did=" . $description['id'] ?>">
                                            <div class="thumbnail" style="background-image:url(<?php echo get_site_url(null, '/wp-content/plugins/policycloud-marketplace/public/assets/img/placeholder.jpg') ?>)">
                                            </div>
                                            <div class="description">
                                                <h4><?php echo $description['info']['title'] ?></h4>
                                                <div class="date">Uploaded: <?php echo date('d/m/y H:i:s', strtotime($description['metadata']['uploadDate'])) ?></div>
                                                <div class="excerpt"><?php echo $description['info']['short_desc'] ?></div>
                                            </div>
                                        </a>
                                    </li>
                                <?php
                                }
                            } else {
                                ?>
                                <p class="policycloud-account-notice">Upload your first asset to get started.</p>
                            <?php
                            }
                            ?>
                        </ul>
                    </section>
                    <section class="policycloud-account-likes">
                        <header>
                            <h3>Likes</h3>
                        </header>
                        <p>Coming soon!</p>
                    </section>
                    <section class="policycloud-account-details">
                        <header>
                            <h3>Details</h3>
                            <button id="policycloud-marketplace-account-edit-toggle">Edit</button>
                        </header>
                        <form id="policycloud-marketplace-account-edit" action="">
                            <table class="information">
                                <tr>
                                    <td>
                                        Username
                                    </td>
                                    <td>
                                        <span>
                                            <?php
                                            echo ($token->username ?? '-');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Password
                                    </td>
                                    <td>
                                        <span class="folding visible">*****************</span>
                                        <input class="folding" type="password" name="policycloud-marketplace-password" placeholder="Enter your new password here" />
                                        <input class="folding" type="password" name="policycloud-marketplace-password-confirm" placeholder="Confirm new password here" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Role
                                    </td>
                                    <td>
                                        <span>
                                            <?php echo ($token->account->role == 'admin') ? 'Administrator' : 'User'; ?>
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
                                            echo ($token->info->title ?? '') . ' ' . ($token->info->name ?? '') . ' ' . ($token->info->surname ?? '');
                                            ?>
                                        </span>
                                        <input class="folding" type="text" name="policycloud-marketplace-title" placeholder="Title (<?php echo ($token->info->title ?? ''); ?>)" />
                                        <input class="folding" type="text" name="policycloud-marketplace-name" placeholder="Name (<?php echo ($token->info->name ?? ''); ?>)" />
                                        <input class="folding" type="text" name="policycloud-marketplace-surname" placeholder="Surname (<?php echo ($token->info->surname ?? ''); ?>)" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Gender
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($token->info->gender ?? '-');
                                            ?>
                                        </span>
                                        <select name="policycloud-marketplace-gender" class="folding">
                                            <option value="male" <?php echo ($token->info->gender == 'male' ? 'selected' : '') ?>>Male</option>
                                            <option value="female" <?php echo ($token->info->gender == 'female' ? 'selected' : '') ?>>Female</option>
                                            <option value="other" <?php echo ($token->info->gender == 'other' ? 'selected' : '') ?>>Other</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Organization
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($token->info->organization ?? '-');
                                            ?>
                                        </span>
                                        <input class="folding" type="text" name="policycloud-marketplace-organization" placeholder="<?php echo ($token->info->organization ?? ''); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        E-mail
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($token->info->email ?? '-');
                                            if ($token->account->verified != '1') {
                                                ?>
                                                <span class="unverified">(Unverified)</span>
                                                <button id="policycloud-marketplace-resend-verification-email">Resend verification email</button>
                                                <?php
						                    print_r($token);
                                            } else {
                                                echo ($token->profile_parameters->public_email == 0) ? ' (Private)' : ' (Public)';
                                            }
                                            ?>
                                        </span>
                                        <input class="folding" type="text" name="policycloud-marketplace-email" placeholder="<?php echo ($token->info->email ?? 'Enter your email address here'); ?>" />
                                        <label for="policycloud-marketplace-email" class="folding">Changing this setting will require a verification of the new e-mail address.</label>
                                        <select name="policycloud-marketplace-public-email" class="folding">
                                            <option value="1" <?php echo ($token->profile_parameters->public_email == 1 ? 'selected' : '') ?>>Public</option>
                                            <option value="0" <?php echo ($token->profile_parameters->public_email == 0 ? 'selected' : '') ?>>Private</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone number
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            if (!empty($token->info->phone)) {
                                                echo ($token->info->phone) . (($token->profile_parameters->public_phone == 0) ? ' (Private)' : ' (Public)');
                                            } else echo '-';
                                            ?>
                                        </span>
                                        <input class="folding" type="text" name="policycloud-marketplace-phone" placeholder="<?php
                                                                                                                                echo (empty($token->info->phone) ? 'Enter your phone number here' : $token->info->phone); ?>" />
                                        <select name="policycloud-marketplace-public-phone" class="folding">
                                            <option value="1" <?php echo ($token->profile_parameters->public_phone == 1 ? 'selected' : '') ?>>Public</option>
                                            <option value="0" <?php echo ($token->profile_parameters->public_phone == 0 ? 'selected' : '') ?>>Private</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Website
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($token->info->webpage ?? '-');
                                            ?>
                                        </span>
                                        <input class="folding" type="text" name="policycloud-marketplace-webpage" placeholder="<?php
                                                                                                                                echo ($token->info->webpage ?? 'https://www.example.org/'); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Member since
                                    </td>
                                    <td>
                                        <?php
                                        echo date('d/m/y', strtotime($token->account->registration_datetime))
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <div class="folding error"></div>
                            <button type="submit" class="folding">Submit</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
<?php
    }
}
