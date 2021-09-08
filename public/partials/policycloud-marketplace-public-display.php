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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

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
                            <input required name="username" class="form-control" placeholder="Username" id="username" type="text">
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                            </div>
                            <input required name="password" class="form-control" placeholder="Password" type="password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block submit-login"> Log In </button>
                    </div> <!-- form-group// -->
                    <div class="login-error">
                        <p class="text-center">
                            <!-- TODO: Add forgot password page link. -->
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

function read_multiple_html($description_objects)
{
    /**
     * TODO @elefkour: Εκτύπωση sidebar φίλτρων. 
     * Σημείωση: Ανάγνωση $_GET για ενεργοποίηση ήδη ενεργών φίλτρων.
     * (π.χ. $_GET['category'] == 'ταδε' να εκτυπώνει checked το checkbox του 'ταδε' category).
     */


    /**
     * TODO @elefkour: Εκτύπωση λίστας αντικειμένων $description_objects (με foreach).
     * Σχήμα δεδομένων:
     */
?>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <div class="sidenav">
        <a>Filter by</a>

        <form action="">
            <input type="text" style="width:100%;" name="type" placeholder="Search..">
        </form>
        <button class="dropdown-btn">Advanced Search
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            ​<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
            <div id="my_kws">
                <div class="form-row">
                    <form action="">
                        ​<div class="row">
                            <span class="col-sm">
                                ​ <input type="text" size="5" name="kw" placeholder="Add your keyword..." />
                            </span><span class="col-sm">
                                ​<input type="text" size="5" name="kval" placeholder="Add your value..." /> <button class="remove"><i class="fa fa-close"></i></button>
                            </span>
                        </div>
                </div>
                <div><button class="add" id="add"><i class="fas fa-plus"></i></button></div>
            </div>
            </form>
        </div>
        <button class="dropdown-btn">Asset Types
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="http://localhost/marketplace/discover?type=algorithms">Algorithms</a>
            <a href="http://localhost/marketplace/discover?type=tools">Tools</a>
            <a href="http://localhost/marketplace/discover?type=datasets">Datasets</a>
            <a href="http://localhost/marketplace/discover?type=outcomes">Project's Outcomes</a>
            <a href="http://localhost/marketplace/discover?type=webinars">Webinars</a>
            <a href="http://localhost/marketplace/discover?type=tutorials">Tutorials</a>
        </div>
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="#clients">Clients</a>
        <a href="#contact">Contact</a>


    </div>
    <div class="main">
        <h1>The display Property:</h1>

        <div class="grid-container">
            <?php
            foreach ($description_objects as $someObject) {

                //$id = $someObject['id'];
                $asset_name = $someObject['info']['title'];
                //$info=$some0bject['info'];
                //echo $id;
                $asset_fieldofuse = $someObject['info']['fieldOfUse'];
                $asset_collection = $someObject['collection'];
                $asset_subtype = $someObject['info']['subtype'];
                $short_desc = $someObject['info']['short_desc'];

            ?>

                <div class="card">
                    <div class='container1'>
                        <div class="photo"> <img src="http://localhost/marketplace/wp-content/uploads/2021/06/aac315_584ccfe01d2941cc9c2abae9e937d316_mv2.jpeg">
                            <div class="photos"><?php echo $asset_collection; ?> </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="card-link" style="color:gray;font-size:12px;"><i class="far fa-user"></i> University of Nicosia | <i class="far fa-eye"></i> 100 | <i class="far fa-calendar-alt"></i> 2.23.2021</span>
                        <b>
                            <h4><?php echo $asset_name; ?></h4>
                        </b>
                        <p class="card-text"><?php echo $short_desc; ?></p>

                    </div>
                </div>

            <?php
            }
            ?>
        </div>

        <p>Set the <em>display</em> property to <em>inline-grid</em> to make an inline grid container.</p>

        Next
        Copy
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
            dropdown[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        }
    </script>

<?php
}

function upload_step()
{ ?>
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-10 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
                <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                    <h2 id="heading">Upload your Project</h2>
                    <p>Fill all form field to go to next step</p>
                    <form id="msform">
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
                                <script>
                                    CKEDITOR.replace('editor1');
                                </script>
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
                                        <h5 class="purple-text text-center">You Have Successfully Signed Up</h5>
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
?>