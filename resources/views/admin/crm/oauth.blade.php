{{-- resources/views/admin/crm/oauth.blade.php --}}

@php
    /**
     * This view handles the OAuth connection flow for a CRM.
     *
     * @var string $type The type of connection ('agency' or other).
     * @var string $id The ID for the connection.
     */
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect to CRM</title>
    {{-- It's recommended to include Bootstrap and other CSS/JS files in a main layout file --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Added some basic styling for better presentation */
        #mainbody {
            padding: 2rem;
        }

        .section {
            display: none;
            /* Hide all sections by default */
        }

        .first {
            display: block;
            /* Show the first section initially */
        }

        #otp .form-control {
            width: 50px;
        }

        .text-link {
            cursor: pointer;
            color: #0d6efd;
        }
    </style>
</head>

<body>

    <div id="mainbody">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-crm text-danger w-100 text-center mb-3"></div>

                {{-- Section: Already Logged In --}}
                <div class="row section text-center" id="last_login">
                    <div class="col-12 d-flex flex-column align-items-center justify-content-center">
                        <h3 class="mt-1">Already Logged In</h3>
                        <h5 id="last_login_email" class="text-muted"></h5>
                        <div class="d-flex justify-content-center mt-2">
                            <button class="btn btn-primary px-4 m-1 reconnect">Connect Now</button>
                            <button class="btn btn-danger px-4 m-1 last_logout" data-prev="logincrm">Logout</button>
                        </div>
                    </div>
                </div>

                {{-- Section: CRM Login Form --}}
                <div class="row section first" id="logincrm">
                    <div class="col-12">
                        <h3 class="text-center mt-3">Login to CRM</h3>
                        <p class="text-center text-muted">Use your CRM credentials, not the credentials for this portal.
                        </p>

                        @if ($type == 'agency')
                            <div class="alert alert-info text-center">
                                You are connecting as an Agency. All locations will be approved.
                            </div>
                        @endif

                        <form id="crmLoginForm">
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="submitdata">Submit</button>
                        </form>
                    </div>
                </div>

                {{-- Section: Multiple Users Found --}}
                <div class="row section" id="multiusers">
                    {{-- This content is generated dynamically by JavaScript --}}
                </div>

                {{-- Section: OTP Verification --}}
                <div class="row section" id="otpcode">
                    <div class="col-12">
                        <button class="btn btn-secondary px-4 backbutton mb-3" data-prev="selectchannel">Back</button>
                        <h5 class="text-center mt-3">Please enter the OTP to verify</h5>
                        <form class="text-center" onsubmit="return false;">
                            <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
                                <input class="m-2 text-center form-control" type="text" inputmode="numeric"
                                    pattern="\d{6}" id="otpcodevalue" maxlength="6" />
                            </div>
                            <div class="form-group d-flex flex-column justify-content-center mt-3">
                                <button class="btn btn-primary px-4 validate" id="sendotp">Validate</button>
                                <div class="mt-3">
                                    <p class="text-link resend">Click here if not received</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Section: Select OTP Channel --}}
                <div class="row section" id="selectchannel">
                    <div class="col-12">
                        <button class="btn btn-secondary px-4 backbutton channelsect mb-3"
                            data-prev="logincrm">Back</button>
                        <h3 class="text-center mt-3">OTP Channel</h3>
                        <p class="text-center">How would you like to receive the OTP for verification?</p>
                        <form id="otpform">
                            <div class="form-check channel cphone mb-2">
                                <input type="radio" name="channel" id="phone" value="phone"
                                    class="form-check-input" checked>
                                <label class="form-check-label" for="phone">Phone: <span
                                        id="cphone"></span></label>
                            </div>
                            <div class="form-check channel cemail mb-3">
                                <input type="radio" name="channel" id="ceemail" value="email"
                                    class="form-check-input">
                                <label class="form-check-label" for="ceemail">Email: <span
                                        id="cemail"></span></label>
                            </div>
                            <input type="hidden" id="token" />
                            <div class="form-group">
                                <button type="button" class="btn btn-primary w-100" id="sendotptype">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Self-invoking function to encapsulate code and avoid global scope pollution
        (function($) {
            // --- Configuration & State ---
            const baseURLloc = 'https://services.leadconnectorhq.com/';
            const locPublic = baseURLloc + 'integrations/public/';
            const loginUrl = baseURLloc + 'oauth/2/login/email';

             const settingsPageUrl = '/admin/setting';
             let sessionKey = '';
            let companyId = '';
            let company_prefix = '';
            const connectionType = '{{ $type }}';
            const connectionId = '{{ $id }}';

            // OAuth configuration
            const oauthRedirects = {
                'Location': '{{ env('CRM_OAUTH_CALLBACK_URL_LOCATION', 'http://127.0.0.1:8000/authorization/crm/oauth/callback') }}',
                'Company': '{{ env('CRM_OAUTH_CALLBACK_URL_COMPANY', 'http://127.0.0.1:8000/authorization/crm/oauth/callback') }}'
            };
            const crmScopes = {
                'Location': 'users.readonly contacts.readonly contacts.write calendars/events.readonly calendars/events.write conversations.readonly conversations.write conversations/message.write conversations/message.readonly locations/customFields.readonly medias.write medias.readonly',
                'Company': 'users.readonly contacts.readonly contacts.write calendars/events.readonly calendars/events.write conversations.readonly conversations.write conversations.write conversations/message.write conversations/messages.readonly locations/customFields.readonly medias.write medias.readonly'
            };

            // --- Helper Functions ---
            function showError(msg = '') {
                $(".error-crm").text(msg);
            }

            function showSection(sectionId) {
                $(".section").hide();
                $(`#${sectionId}`).show();
            }

            function getAuthHeaders() {
                const headers = new Headers();
                if (sessionKey) {
                    headers.append("Authorization", "Bearer " + sessionKey);
                }
                return headers;
            }

            // --- Core Logic Functions ---
            function handleFinalRedirect(url) {
                console.log("Final redirect URL:", url);
                fetch(`${url}&sessionKey=${sessionKey}&onlyjson=1${company_prefix}`)
                    .then(response => response.text())
                    .then(data => {
                        console.log("Connection status:", data);
                        if (data === 'Connected successfully') {
                            window.location.href = settingsPageUrl;
                        } else {
                            showError(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error in final connection step:', error);
                        showError('An error occurred while finalizing the connection.');
                    });
            }

            function handleOauth() {
                showError('Please wait... connecting to CRM.');

                const userType = (connectionType === 'agency') ? 'Company' : 'Location';
                const clientId = (userType === 'Location') ? "{{ env('CRM_CLIENT_ID') }}" : "{{ env('CRM_CLIENT_ID') }}";
                const entityId = (userType === 'Location') ? connectionId : companyId;

                const callbackUrl = oauthRedirects[userType];
                const scope = crmScopes[userType];
                const keyloc = (userType === 'Company') ? 'company_id' : 'location_id';

                if (!entityId) {
                    showError(`Error: ${userType} ID is missing.`);
                    return;
                }

                const authUrl =
                    `${baseURLloc}oauth/authorize?client_id=${clientId}&${keyloc}=${entityId}&response_type=code&redirect_uri=${callbackUrl}&scope=${scope}&userType=${userType}`;

                const requestOptions = {
                    method: 'POST',
                    headers: getAuthHeaders(),
                };

                if (connectionType === 'agency') {
                    requestOptions.headers.append("Content-Type", "application/json;charset=UTF-8");
                    requestOptions.body = JSON.stringify({
                        "approveAllLocations": true
                    });
                }

                fetch(authUrl, requestOptions)
                    .then(response => response.json())
                    .then(result => {
                        console.log('OAuth response:', result);
                        if (result?.redirectUrl) {
                            handleFinalRedirect(result.redirectUrl);
                        } else if (result?.message) {
                            showError(result.message);
                        } else if (result?.error_description) {
                            showError(result.error_description);
                        } else {
                            showError('An unknown error occurred during OAuth authorization.');
                        }
                    })
                    .catch(error => {
                        console.error('OAuth fetch error:', error);
                        showError('Failed to initiate OAuth flow. Please check console for details.');
                    });
            }

            function getCompany(apiKey) {
                return new Promise((resolve, reject) => {
                    const requestOptions = {
                        method: 'GET',
                        headers: getAuthHeaders(),
                    };
                    fetch(locPublic + 'company', requestOptions)
                        .then(res => res.json())
                        .then(data => {
                            if (data?.company) {
                                company_prefix =
                                    `&company_id=${data.company.id}&company_name=${data.company.name}`;
                                resolve(data.company);
                            } else {
                                showError(data.message || 'Could not retrieve company details.');
                                reject(data);
                            }
                        }).catch(err => {
                            showError('Failed to fetch company details.');
                            reject(err);
                        });
                });
            }


            function doConnection() {
                console.log('Starting connection process...');
                console.log('Connection Type:', connectionType);
                console.log('Session Key:', sessionKey);
                if (connectionType === 'agency') {
                    getCompany(sessionKey).then(company => {
                        companyId = company.id;
                        handleOauth();
                    });
                } else {
                    company_prefix = '';
                    handleOauth();
                }
            }

            function makeApiCall(payload = {}) {
                console.log('Making API call with payload:', payload);
                showError(''); // Clear previous errors
                const credentials = {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    ...payload
                };

                if (companyId) {
                    credentials.companyId = companyId;
                }

                fetch(loginUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(credentials)
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('API response data:', data);
                        if (data?.message) {
                            showError(data.message);
                            return;
                        }

                        $('.backbutton.channelsect').attr('data-prev', 'logincrm');

                        if (data?.multipleUsers) {
                            $('.backbutton.channelsect').attr('data-prev', 'multiusers');
                            const usersHtml = `
                        <div class="col-12">
                            <button class="btn btn-secondary px-4 backbutton mb-3" data-prev="logincrm">Back</button>
                            <h3 class="text-center mt-3">Select Account to Connect</h3>
                            <form id="userSelectForm">
                                ${data.multipleUsers.map(user => `
                                        <div class="form-check mb-2">
                                            <input type="radio" name="user" id="user${user.companyId}" value="${user.companyId}" class="form-check-input">
                                            <label for="user${user.companyId}" class="form-check-label">${user.userName} (${user.agencyName})</label>
                                        </div>
                                    `).join('')}
                                <button type="button" class="btn btn-primary w-100 mt-2" id="selectuser">Submit</button>
                            </form>
                        </div>`;
                            $("#multiusers").html(usersHtml);
                            showSection('multiusers');
                        } else if (data?.phone || data?.email) {
                            $(".channel").hide();
                            if (data.phone) {
                                $(".cphone.channel").show();
                                $("#cphone").text(data.phone);
                            }
                            if (data.email) {
                                $(".cemail.channel").show();
                                $("#cemail").text(data.email);
                            }
                            $("#token").val(data.token || '');
                            showSection('selectchannel');
                        } else if (data?.apiKey) {
                            sessionKey = data.apiKey;
                            localStorage.setItem('sessionKey', sessionKey);
                            localStorage.setItem('login_email', credentials.email);
                            doConnection();
                        } else if (data?.token) {
                            $("#token").val(data.token);
                            showSection('otpcode');
                        }
                    })
                    .catch(error => {
                        console.error('API call error:', error);
                        showError('An unexpected error occurred. Please try again.');
                    });
            }

            function resendOTP(isResend = false) {
                const otpChannel = $('input[name="channel"]:checked').val();
                const token = $("#token").val();

                makeApiCall({
                    otpChannel,
                    token
                });

                if (isResend) {
                    const resendButton = $('.resend');
                    showError('New code sent.');
                    resendButton.hide();
                    setTimeout(() => {
                        showError('');
                        resendButton.show();
                    }, 15000);
                }
            }

            // --- Event Handlers ---
            $(document).ready(function() {
                const loggedInEmail = localStorage.getItem('login_email');
                sessionKey = localStorage.getItem('sessionKey');

                if (loggedInEmail && sessionKey) {
                    $('#last_login_email').text(loggedInEmail);
                    showSection('last_login');
                } else {
                    showSection('logincrm');
                }

                // Using event delegation for dynamically added elements
                $('body').on('submit', '#crmLoginForm', function(e) {
                    e.preventDefault();
                    makeApiCall();
                });

                $('body').on('click', '.reconnect', function() {
                    doConnection();
                });

                $('body').on('click', '.last_logout', function() {
                    localStorage.removeItem('login_email');
                    localStorage.removeItem('sessionKey');
                    sessionKey = '';
                    companyId = '';
                    showSection($(this).data('prev'));
                });

                $('body').on('click', '.backbutton', function() {
                    const prevSection = $(this).data('prev');
                    if (prevSection === 'logincrm') {
                        companyId = ''; // Reset company selection
                    }
                    showSection(prevSection);
                });

                $('body').on('click', '#selectuser', function() {
                    const selectedUser = $('input[name="user"]:checked').val();
                    if (selectedUser) {
                        companyId = selectedUser;
                        makeApiCall();
                    } else {
                        showError('Please select an account.');
                    }
                });

                $('body').on('click', '#sendotptype', function() {
                    resendOTP(false);
                    showSection('otpcode');
                });

                $('body').on('click', '.resend', function() {
                    resendOTP(true);
                });

                $('body').on('click', '#sendotp', function() {
                    const otp = $('#otpcodevalue').val();
                    const token = $("#token").val();
                    const otpChannel = $('input[name="channel"]:checked').val();
                    console.log('Validating OTP:', otp);
                    console.log('Using token:', token);
                    console.log('OTP Channel:', otpChannel);
                    console.log('Length of OTP:', otp.length);

                    if (otp.length === 6) {
                        makeApiCall({
                            otp,
                            token,
                            otpChannel
                        });
                    } else {
                        showError('Please enter a valid 6-digit OTP.');
                    }
                });
            });

        })(jQuery);
    </script>

</body>

</html>
