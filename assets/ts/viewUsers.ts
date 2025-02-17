import Api from "./services/Api";
console.log(`Dans viewUser.ts`);

// Si la page a l'id dynamical-user alors je continue le script
const id = document.getElementById("dynamical-user");

if (id) {
  console.log(
      `Dans la page qui contient l'élément qui a pour id dynamical-user`,
  );

  Api.loadUsersFromApi()
      .then((data) =>{
        console.log("Donnés utilisateurs", data);

        const usersList = document.querySelector(".users-list");

        if(!usersList){
          console.error("Impossible de trouver l'élément .user-list");
          return;
        }
        usersList.innerHTML = `<h2>Liste des utilisateurs</h2>`;
        let users = data.users;

        if(users && users.length > 0){
          users.forEach((user) =>{
            const userSection = document.createElement("section");
            userSection.classList.add(
                "d-flex",
                "gap-2",
                "border-1",
                "my-3",
                "align-items-center"
            );
            userSection.setAttribute("data-userid", user.id);
            userSection.setAttribute("data-username", user.name);
            userSection.setAttribute("data-email", user.email);

            userSection.innerHTML = `
                <p>${user.id}</p>
                <p class="name">${user.name}</p>
                <p class="email">${user.email}</p>
                <button class="btn btn-danger">Supprimer</button>
                <button class="btn btn-warning">Modifer</button>
                <button class="btn btn-primary">Voir</button>
            `;
            usersList.appendChild(userSection);

            // Gestion du click sur les boutons .btn-danger
            const btnDangers = document.querySelectorAll(".btn-danger");
            manageDelete(btnDangers);
            const btnEdit = document.querySelectorAll(".btn-warning");
            manageEdit(btnEdit);
            const btnShow = document.querySelectorAll(".btn-primary");
            manageShow(btnShow);


          });
        } else {
          const noUsers = document.createElement("p");
          noUsers.textContent = "Aucun utilisateur trouvé";
          usersList.appendChild(noUsers);
        }
      })
      .catch((e) => {
        console.error(`Erreur attrapée, ${e}`);
      })

  // Gestion de l'ajout d'un utilisateur
  const formAdd = document.querySelector("#form-add-user") as HTMLFormElement;
  manageAdd(formAdd);


}


function manageAdd(formAdd: HTMLFormElement) {
  formAdd.addEventListener("submit", (event: Event) => {
    // Le formulaire n'envoie pas directement d'information
    event.preventDefault();

    // Récupération des données via formData
    const formData = new FormData(formAdd);
    const addedUser = {
      name: formData.get("name") as string,
      email: formData.get("email") as string,
      password: formData.get("password") as string,
    };
    console.log(`addedUser`, addedUser);
    Api.addUserFromApi(addedUser);
  });
}
function manageDelete(btnDangers: NodeListOf<Element>) {
  btnDangers.forEach((btn) => {
    btn.addEventListener("click", (event: Event) => {
      console.log(`Click sur le bouton de suppression`);

      // Suppression du div parent pour l'affichage
      const parentSection = (event.target as HTMLElement).parentElement;
      if (parentSection) {
        const userId = parentSection.getAttribute("data-userid");

        // On cache l'élément du DOM
        parentSection.style.display = "none";

        // Appel de la requête delete (api)
        if (userId) {
          Api.deleteUserFromApi(userId)
            .then((data) => {
              console.log(data);
              // Test si la suppression a bien eu lieu
              if ("delete" in data && data.delete == "true") {
                parentSection.remove();
              }
            })
            .catch((error) => {
              console.error("Erreur attrapée dans viewUser.ts", error);
              setTimeout(() => {
                parentSection.style.display = "flex";
              }, 3000);
            });
        }
      }
    });
  });
}
function manageEdit(btnEdit: NodeListOf<Element>){
  btnEdit.forEach((btn) => {
    btn.addEventListener("click", (event: Event) =>{
      console.log("click sur btn edit");
      const parentSection = (event.target as HTMLElement).parentElement;
      if(!parentSection){
        return;
      }
      const nameElement = parentSection.querySelector("p.name");
      //const nameElement = parentSection.querySelector("p:nth-child(2)")
      // const nameElement = parentSection.dataset.username;
      //const nameElement = parentSection.getAttribute("data-username");
      const emailElement = parentSection.querySelector("p.email");
      // const emailElement = parentSection.getAttribute("data-email");
      const userId = parentSection.getAttribute("data-userid");
      if (nameElement && emailElement && userId){
        // je voudrais modifier les balises <p> en input
        const nameInput = document.createElement("input");
        nameInput.type = "text";
        nameInput.value = nameElement.textContent || '';

        const emailInput = document.createElement("input");
        emailInput.type = "email";
        emailInput.value = emailElement.textContent || '';

        nameElement.replaceWith(nameInput);
        emailElement.replaceWith(emailInput);

        // J'ajoute ensuite un bouton pour sauvegarder
        const saveBtn = document.createElement("button");
        saveBtn.className = "btn btn-success";
        saveBtn.textContent = "Enregistrer";
        parentSection.appendChild(saveBtn);

        saveBtn.addEventListener("click", () => {
          const newName = nameInput.value;
          const newEmail = emailInput.value;
          console.log({
            id: userId,
            name: newName,
            email: newEmail
          });
          Api.editUserFromApi({id: userId, name: newName, email: newEmail});
        });
      }
    })
  })
}

function manageShow(btnShow: NodeListOf<Element>){
  btnShow.forEach((btn) => {
    btn.addEventListener('click', (event: Event) =>{
      console.log("click sur le btn Voir");
      const parentSection = (event.target as HTMLElement).parentElement;
      if(!parentSection){
        return;
      }
      const userId = parentSection.getAttribute("data-userid");
      if (userId){
        Api.loadUserFromApi(userId);
      }
    })
  })
}
console.log("fin");