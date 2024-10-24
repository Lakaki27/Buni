const inviteBtn = document.getElementById('inviteMember')
const inviteMemberURL = "/teams/controllers/inviteHandler.php";
const launchBtn = document.getElementById('launchDailyScrum')
const launchDailyScrumURL = "/teams/controllers/scrumCreationHandler.php";

inviteBtn.addEventListener('click', createInvitePrompt)
launchBtn.addEventListener('click', launchDailyScrum)

async function createInvitePrompt() {
    const { value: mail } = await Swal.fire({
        title: "E-mail address: ",
        input: "text",
        inputLabel: "E-mail address: ",
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return "You need to write something!";
            }
        }
    });
    if (mail && /\S+@\S+\.\S+/.test(mail)) {
        inviteMember(mail)
    } else {
        Toast.fire({
            icon: 'error',
            title: "Invalid e-mail address."
        })
    }
}

async function inviteMember(mail) {
    let inviteParameters = {
        mail: mail,
        teamName: currentTeamName
    };

    try {
        const response = await fetchController(inviteMemberURL, {
            body: JSON.stringify(inviteParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Unable to send invite."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
        } else {
            Toast.fire({
                icon: "success",
                title: "Invite sent !"
            })
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Unable to send invite."
        })
    }
}

async function launchDailyScrum() {
    let scrumParameters = {
        teamName: currentTeamName
    };

    try {
        const response = await fetchController(launchDailyScrumURL, {
            body: JSON.stringify(scrumParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Unable to start daily scrum."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
        } else if (resp?.success) {
            window.location = `${window.location.origin}/scrum?id=${resp.success}`
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Error while launching scrum."
        })
    }
}