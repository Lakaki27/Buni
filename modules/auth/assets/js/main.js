const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const loginURL = "/auth/controllers/loginHandler.php";
const registerURL = "/auth/controllers/registerHandler.php";

loginForm.addEventListener("submit", (e) => {
    e.preventDefault();
    login();
});

registerForm.addEventListener("submit", (e) => {
    e.preventDefault();
    register();
});

async function login() {
    let loginParameters = {
        mail: document.getElementById("loginMail").value,
        password: document.getElementById("loginPassword").value,
    };

    try {
        const response = await fetchController(loginURL, {
            body: JSON.stringify(loginParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Auth error."
            });
        }

        const json = await response.json();

        if (json?.error) {
            Toast.fire({
                icon: "error",
                title: json.error
            })
        } else {
            window.location = window.location.origin
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Auth error."
        })
    }
}


async function register() {
    let registerParameters = {
        username: document.getElementById("registerUsername").value,
        lastName: document.getElementById("registerLastName").value,
        firstName: document.getElementById("registerFirstName").value,
        mail: document.getElementById("registerMail").value,
        password: document.getElementById("registerPassword").value,
        confirmPassword: document.getElementById("registerPasswordConfirm").value,
    };

    if (registerParameters.password !== registerParameters.confirmPassword) {
        Toast.fire({
            icon: "error",
            title: "Passwords do not match."
        });
        return;
    }

    try {
        const response = await fetchController(registerURL, {
            body: JSON.stringify(registerParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Register error."
            });
        }

        const json = await response.json();

        if (json?.error) {
            Toast.fire({
                icon: "error",
                title: json.error
            });
        } else if (json?.success) {
            window.location = window.location.origin
        }
    } catch (error) {
        if (json?.error) {
            Toast.fire({
                icon: "error",
                title: "Register error."
            });
        }
    }
}