<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

function registration_form_html()
{
    /**
     * 
     * TODO@elefkour: Δημιουργία function με το HTML registration form.
     * Βάλε ως κύριο id=policycloud-registration για το HTML <form>.
     * Βλέπε το documentation (https://documenter.getpostman.com/view/16776360/TzsZs8kn#17a87988-323b-4209-b93c-ea3854616ab3)
     * "Register a User" για τα πεδία που χρειάζονται.
     */
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
                            <input required class="form-control" placeholder="Repeat password" type="password">
                        </div> <!-- form-group// -->
                        <div class="form-group">

                            <button type="submit" class="btn btn-primary btn-block"> Create Account </button>
                        </div> <!-- form-group// -->
                    </div>
                    <p class="text-center">Have an account? <a href="">Log In</a> </p>
                </form>
            </article>
        </div> <!-- card.// -->

    </div>

<?php
}

?>