const createTeamBtn = document.getElementById('createTeamBtn')

createTeamBtn.addEventListener('click', createTeamPrompt())

async function createTeamPrompt() {
    const { value: teamName } = await Swal.fire({
        title: "Enter the name of your new team",
        input: "text",
        inputLabel: "Team name",
        inputValue,
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return "You need to write something!";
            }
        }
    });
    if (teamName) {
        
    }
}