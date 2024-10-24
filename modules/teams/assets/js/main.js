const createTeamBtn = document.getElementById('createTeamBtn')
const createTeamURL = "/teams/controllers/createTeamHandler.php";
const invitesDiv = document.getElementById('invites')
const changeInviteStateURL = "/teams/controllers/changeInviteStateHandler.php"

async function createTeamPrompt() {
    const { value: teamName } = await Swal.fire({
        title: "Enter the name of your new team",
        input: "text",
        inputLabel: "Team name",
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return "You need to write something!";
            }
        }
    });
    if (teamName && /^[a-zA-Z0-9]{3,30}$/gm.test(teamName)) {
        trelloIDPrompt(teamName)
    } else {
        Toast.fire({
            icon: 'error',
            title: "Invalid team name."
        })
    }
}

async function trelloIDPrompt(teamName) {
    const { value: boardID } = await Swal.fire({
        title: "Trello Board ID: ",
        input: "text",
        inputLabel: "Board ID",
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return "You need to write something!";
            }
        }
    });
    if (boardID) {
        createTeam(teamName, boardID)
    } else {
        Toast.fire({
            icon: 'error',
            title: "Invalid trello board ID."
        })
    }
}

async function createTeam(name, boardID) {
    let loginParameters = {
        name: name,
        teamId: boardID
    };

    try {
        const response = await fetchController(createTeamURL, {
            body: JSON.stringify(loginParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Error while creating team."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
        } else {
            window.location = `${window.location.origin}/viewTeam?t=${name}`
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Auth error."
        })
    }
}


async function changeInviteState(id, accept) {
    let loginParameters = {
        inviteId: id,
        accept: accept
    };

    try {
        const response = await fetchController(changeInviteStateURL, {
            body: JSON.stringify(loginParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Error while creating team."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
        } else {
            window.location.reload()
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Auth error."
        })
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (window.location.search === "?create") {
        createTeamPrompt()
    }

    createTeamBtn.addEventListener('click', createTeamPrompt)

    document.querySelectorAll('.viewTeamBtn').forEach((btn) => {
        btn.addEventListener('click', function (e) {
            window.location.replace(window.location.origin + "/viewTeam?t=" + this.id);
        })
    })
})

invitesDiv.addEventListener('click', function (e) {
    if (e.target.nodeName === "BUTTON") {
        if (e.target.classList.contains("acceptBtn")) {
            changeInviteState(parseInt(e.target.id), true)
        } else if (e.target.classList.contains("refuseBtn")) {
            changeInviteState(parseInt(e.target.id), false)
        }
    }
})