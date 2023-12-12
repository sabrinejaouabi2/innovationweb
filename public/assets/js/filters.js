window.onload = () => {
    const FiltersForm = document.querySelector("#filters");

    document.querySelectorAll("#filters input").forEach(input => {
        input.addEventListener("change", () => {
            // Ici on intercepte les clics
            // On récupère les données du formulaire
            const Form = new FormData(FiltersForm);

            // On fabrique la "queryString"
            const Params = new URLSearchParams();

            Form.forEach((value, key) => {
                Params.append(key, value);
            });
            // On récupère l'url active
            const Url = new URL(window.location.href);

            // On lance la requête ajax
            fetch(Url.pathname + "?" + Params.toString() +"&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response =>
                response.json()
            ).then(data=> {
            const content=document.querySelector("#content");
            content.innerHTML=data.content;
            }).catch(e=> alert(e));

        });
    });

}