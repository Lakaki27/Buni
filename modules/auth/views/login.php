<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;" id="pageContainer">
    <div class="container bg-white shadow-sm rounded-3 p-4" id="formContainer">
        <div class="p-2 d-flex justify-content-center align-items-center">
            <img id="imgLogo" src="/assets/img/buni-logo.png" alt="Buni">
            <h1 style="font-size: 65px;">Buni</h1>
        </div>
        <div class="d-flex">
            <div id="loginDiv" class="container-sm align-self-center">
                <div class="w-100 d-flex justify-content-center">
                    <h2>Login</h2>
                </div>
                <form id="loginForm" class="row g-3">
                    <div class="col-12">
                        <label for="loginMail" class="form-label">E-mail :</label>
                        <input type="email" name="loginMail" id="loginMail" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="loginPassword" class="form-label">Mot de passe :</label>
                        <input type="password" name="loginPassword" id="loginPassword" class="form-control">
                    </div>
                    <div class="col-12 w-100 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
            <div id="registerDiv" class="container-sm">
                <div class="w-100 d-flex justify-content-center">
                    <h2>Register</h2>
                </div>
                <form id="registerForm" class="row g-3 align-self-center">
                    <div class="col-12">
                        <label for="registerUsername" class="form-label">Nom d'utilisateur :</label>
                        <input type="text" name="registerUsername" id="registerUsername" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="registerLastName" class="form-label">Nom :</label>
                        <input type="text" name="registerLastName" id="registerLastName" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="registerFirstName" class="form-label">Prénom :</label>
                        <input type="text" name="registerFirstName" id="registerFirstName" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="registerEmail" class="form-label">E-mail du Trello associé :</label>
                        <input type="email" name="registerEmail" id="registerMail" class="form-control">
                    </div>

                    <div class="col-12">
                        <label for="registerPassword" class="form-label">Mot de passe :</label>
                        <input type="password" name="registerPassword" id="registerPassword" class="form-control">
                    </div>

                    <div class="col-12">
                        <label for="registerPasswordConfirm" class="form-label">Confirmez votre mot de passe :</label>
                        <input type="password" name="registerPasswordConfirm" id="registerPasswordConfirm" class="form-control">
                    </div>
                    <div class="col-12 w-100 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/modules/auth/assets/js/main.js"></script>