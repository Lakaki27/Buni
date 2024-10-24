const scrumMasterActionsURL = "/scrums/controllers/realtimeScrumMasterActionsHandler.php"

document.addEventListener('click', function (e) {
    if (e.target.nodeName === "BUTTON") {
        if (e.target.id === "nextStep") {
            changeScrumState("nextStep")
        } else if (e.target.id === "attributeBtn") {
            changeScrumState("nextStep")
            sendAttributions()
        }
    }
})

async function changeScrumState(action) {
    let loginParameters = {
        scrumId: currentScrumId,
        scrumAction: action
    };

    try {
        const response = await fetchController(scrumMasterActionsURL, {
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
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Error while refreshing page."
        })
    }
}

async function sendAttributions() {
    let nodeList = document.querySelectorAll('.attributeSelect')
    let attributions = {}
    nodeList.forEach((selectElem) => {
        attributions[selectElem.id] = {
            trelloId: selectElem.value,
            name: selectElem.options[selectElem.selectedIndex].text
        }
    })

    try {
        const response = await fetchController(voteSubmitHandlerURL, {
            body: JSON.stringify({
                attributions: attributions,
                scrumId: currentScrumId,
                voteType: "attribute"
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
        } else if (resp?.changeRefreshState) {
            setRefreshInterval()
            console.log("set refresh interval.")
        } else {
            console.log("Didn't work.")
        }
    } catch (error) {
        Toast.fire({
            icon: "error",
            title: "Error in vote submit."
        })
    }
}