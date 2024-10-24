const currentScrumId = window.location.search.slice(4)
const scrumHandlerURL = "/scrums/controllers/realtimeScrumHandler.php";
const voteSubmitHandlerURL = "/scrums/controllers/realtimeVotesHandler.php";
const contentDisplayer = document.getElementById('contentDiv')
let refreshInterval = 0;

function setRefreshInterval() {
    refreshInterval = setInterval(() => {
        fakeRefresh()
    }, 500);
}

async function fakeRefresh() {
    let loginParameters = {
        scrumId: currentScrumId
    };

    try {
        const response = await fetchController(scrumHandlerURL, {
            body: JSON.stringify(loginParameters),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Error while refreshing page."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
            if (resp.error === "Scrum has ended.") {
                window.location.replace("/")
            }
        } else {
            contentDisplayer.innerHTML = resp.success
            if (resp?.changeRefreshState) {
                if (resp.changeRefreshState === "stop") {
                    clearInterval(refreshInterval)
                } else if (resp.changeRefreshState === "restart" && (typeof refreshInterval === 'number' && refreshInterval > 0)) {
                    setRefreshInterval()
                } else if (resp.changeRefreshState === "endScrum") {
                    clearInterval(refreshInterval)
                    window.location.replace("/")
                }
            }
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Error while refreshing page."
        })
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setRefreshInterval()
})

document.addEventListener('click', function (e) {
    if (e.target.nodeName === "BUTTON" && e.target.id === "voteBtn") {
        sendVotes()
    }
})

async function sendVotes() {
    let nodeList = document.querySelectorAll('.voteSelect')
    let votes = {}
    nodeList.forEach((selectElem) => {
        votes[selectElem.id] = selectElem.value
    })

    try {
        const response = await fetchController(voteSubmitHandlerURL, {
            body: JSON.stringify({
                votes: votes,
                scrumId: currentScrumId,
                voteType: "vote"
            }),
        });

        if (!response.ok) {
            Toast.fire({
                icon: "error",
                title: "Submit error."
            });
        }

        const resp = await response.json();

        if (resp?.error) {
            Toast.fire({
                icon: "error",
                title: resp.error
            })
        } else {
            setRefreshInterval()
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Error in vote submit."
        })
    }
}