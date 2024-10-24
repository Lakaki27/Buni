const currentTeamName = window.location.search.slice(3)
const joinScrumBtn = document.getElementById('joinDailyScrum')
const joinScrumURL = "/teams/controllers/scrumJoinHandler.php"

joinScrumBtn.addEventListener('click', joinDailyScrum)

async function joinDailyScrum() {
    let inviteParameters = {
        teamName: currentTeamName
    };

    try {
        const response = await fetchController(joinScrumURL, {
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
        } else if (resp?.success) {
            window.location = `${window.location.origin}/scrum?id=${resp.success}`
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Unable to send invite."
        })
    }
}