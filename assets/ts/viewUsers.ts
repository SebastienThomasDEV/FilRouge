import Api from "./services/Api";
console.log(`Dans viewUser.ts`);

// Si la page a l'id dynamical-user alors je continue le script
const id = document.getElementById("dynamical-user");

if (id) {
  console.log(
    `Dans la page qui contient l'élément qui a pour id dynamical-user`
  );
  // Gestion du click sur les boutons .btn-danger
  const btnDangers = document.querySelectorAll(".btn-danger");
  manageDelete(btnDangers);

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
      pwd: formData.get("pwd") as string,
    };
    console.log(`addedUser`, addedUser);
    /* il reste à : 
      - afficher ce nouvel utilisateur en créant un nouvel élement du dom (section)
      - Faire appel à un service qui fera une requête http via la fonction fetch avec la méthode post
    */
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
