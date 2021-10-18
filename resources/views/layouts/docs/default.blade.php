<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        @include('includes.docs...head')
    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-3" id="sidebar">
                    <div class="column-content">
                        <div class="search-header">
                            <img src="/assets/docs/./img/f2m2_logo.svg" class="logo" alt="Logo" />
                            <input id="search" type="text" placeholder="Search">
                        </div>
                        <ul id="navigation">

                            <li><a href="#introduction">Introduction</a></li>

                            

                            <li>
                                <a href="#Login">Login</a>
                                <ul>
									<li><a href="#Login_index">index</a></li>
</ul>
                            </li>


                            <li>
                                <a href="#Register">Register</a>
                                <ul>
									<li><a href="#Register_index">index</a></li>
</ul>
                            </li>


                            <li>
                                <a href="#Request">Request</a>
                                <ul>
									<li><a href="#Request_index">index</a></li>

									<li><a href="#Request_add_message">add_message</a></li>

									<li><a href="#Request_dashboard">dashboard</a></li>
</ul>
                            </li>


                            <li>
                                <a href="#Account">Account</a>
                                <ul>
									<li><a href="#Account_update">update</a></li>

									<li><a href="#Account_change_password">change_password</a></li>
</ul>
                            </li>


                        </ul>
                    </div>
                </div>
                <div class="col-9" id="main-content">

                    <div class="column-content">

                        @include('includes.docs...introduction')

                        <hr />

                                                

                                                <a href="#" class="waypoint" name="Login"></a>
                        <h2>Login</h2>
                        <p></p>

                        
                        <a href="#" class="waypoint" name="Login_index"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>POST</h2></li>
                            <li><h3>index</h3></li>
                            <li>api/login</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">User Login</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/login" type="POST">--}}
                          <div class="endpoint-paramenters">
                            <h4>Parameters</h4>
                            <ul>
                              <li class="parameter-header">
                                <div class="parameter-name">PARAMETER</div>
                                <div class="parameter-type">TYPE</div>
                                <div class="parameter-desc">DESCRIPTION</div>
                                <div class="parameter-value">VALUE</div>
                              </li>
                                                           <li>
                                <div class="parameter-name">email</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Email Address</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="email">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">password</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Password</div>
                                <div class="parameter-value">
                                    <input type="password" class="parameter-value-text" name="password">
                                </div>
                              </li>

                            </ul>
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="POST"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>
                        

                                                <a href="#" class="waypoint" name="Register"></a>
                        <h2>Register</h2>
                        <p></p>

                        
                        <a href="#" class="waypoint" name="Register_index"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>POST</h2></li>
                            <li><h3>index</h3></li>
                            <li>api/register</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">Register a new user</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/register" type="POST">--}}
                          <div class="endpoint-paramenters">
                            <h4>Parameters</h4>
                            <ul>
                              <li class="parameter-header">
                                <div class="parameter-name">PARAMETER</div>
                                <div class="parameter-type">TYPE</div>
                                <div class="parameter-desc">DESCRIPTION</div>
                                <div class="parameter-value">VALUE</div>
                              </li>
                                                           <li>
                                <div class="parameter-name">first_name</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">First Name</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="first_name">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">last_name</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Last Name</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="last_name">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">email</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Email Address</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="email">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">membership</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Membership</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="membership">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">password</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Password</div>
                                <div class="parameter-value">
                                    <input type="password" class="parameter-value-text" name="password">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">number</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Phone Number</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="number">
                                </div>
                              </li>

                            </ul>
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="POST"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>
                        

                                                <a href="#" class="waypoint" name="Request"></a>
                        <h2>Request</h2>
                        <p></p>

                        
                        <a href="#" class="waypoint" name="Request_index"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>GET</h2></li>
                            <li><h3>index</h3></li>
                            <li>api/requests/{record_type_id}</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">Get Requests based on type</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/requests/{record_type_id}" type="GET">--}}
                          <div class="endpoint-paramenters">
                            
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="GET"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>

                        <a href="#" class="waypoint" name="Request_add_message"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>POST</h2></li>
                            <li><h3>add_message</h3></li>
                            <li>api/message/{request_id}</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">Add a new message to a request</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/message/{request_id}" type="POST">--}}
                          <div class="endpoint-paramenters">
                            <h4>Parameters</h4>
                            <ul>
                              <li class="parameter-header">
                                <div class="parameter-name">PARAMETER</div>
                                <div class="parameter-type">TYPE</div>
                                <div class="parameter-desc">DESCRIPTION</div>
                                <div class="parameter-value">VALUE</div>
                              </li>
                                                           <li>
                                <div class="parameter-name">type</div>
                                <div class="parameter-type">int</div>
                                <div class="parameter-desc">Message Type</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="type">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">message</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">(Optional Message)</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="message">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">image</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">(Optional image - File)</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="image">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">image</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">(Optional voice note - File)</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="image">
                                </div>
                              </li>

                            </ul>
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="POST"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>

                        <a href="#" class="waypoint" name="Request_dashboard"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>GET</h2></li>
                            <li><h3>dashboard</h3></li>
                            <li>api/profile/dashboard</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">Get Dashboard Information</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/profile/dashboard" type="GET">--}}
                          <div class="endpoint-paramenters">
                            
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="GET"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>
                        

                                                <a href="#" class="waypoint" name="Account"></a>
                        <h2>Account</h2>
                        <p></p>

                        
                        <a href="#" class="waypoint" name="Account_update"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>PUT</h2></li>
                            <li><h3>update</h3></li>
                            <li>api/profile/update</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc">Update Profile</p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/profile/update" type="PUT">--}}
                          <div class="endpoint-paramenters">
                            <h4>Parameters</h4>
                            <ul>
                              <li class="parameter-header">
                                <div class="parameter-name">PARAMETER</div>
                                <div class="parameter-type">TYPE</div>
                                <div class="parameter-desc">DESCRIPTION</div>
                                <div class="parameter-value">VALUE</div>
                              </li>
                                                           <li>
                                <div class="parameter-name">first_name</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">First Name</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="first_name">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">last_name</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Last Name</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="last_name">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">email</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Email Address</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="email">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">membership</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Membership</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="membership">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">password</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Password</div>
                                <div class="parameter-value">
                                    <input type="password" class="parameter-value-text" name="password">
                                </div>
                              </li>
                             <li>
                                <div class="parameter-name">number</div>
                                <div class="parameter-type">string</div>
                                <div class="parameter-desc">Phone Number</div>
                                <div class="parameter-value">
                                    <input type="text" class="parameter-value-text" name="number">
                                </div>
                              </li>

                            </ul>
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="PUT"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>

                        <a href="#" class="waypoint" name="Account_change_password"></a>
                        <div class="endpoint-header">
                            <ul>
                            <li><h2>PUT</h2></li>
                            <li><h3>change_password</h3></li>
                            <li>api/profile/password</li>
                          </ul>
                        </div>

                        <div>
                          <p class="endpoint-short-desc"></p>
                        </div>
                       <!--  <div class="parameter-header">
                             <p class="endpoint-long-desc"></p>
                        </div> -->
                        {{--<form class="api-explorer-form" uri="api/profile/password" type="PUT">--}}
                          <div class="endpoint-paramenters">
                            
                          </div>
                           {{--<div class="generate-response" >--}}
                              <!-- <input type="hidden" name="_method" value="PUT"> -->
                              {{--<input type="submit" class="generate-response-btn" value="Generate Example Response">--}}
                          {{--</div>--}}
                        {{--</form>--}}
                        <hr>


                    </div>
                </div>
            </div>
        </div>


    </body>
</html>
