/**
 * Javascript for myBlog
 */

 let index = 0;

 let saveInputArray = [];

 /**
  * Save and restore already inputed fields
  * @param {*} id int
  */

 function saveInput(id) {

    var edited = false;

    var social_id = 0;
    var social_name = "";
    var social_image = "";
    var social_meta = "";

    if(document.getElementById(`social_id_${id}`)) {
        var social_id = document.getElementById(`social_id_${id}`).value;
    }

    if(document.getElementById(`social_name_${id}`)) {
        var social_name = document.getElementById(`social_name_${id}`).value;
    }

    if(document.getElementById(`social_image_${id}`)) {
        var social_image = document.getElementById(`social_image_${id}`).value;
    }
    
    if(document.getElementById(`social_meta_${id}`)) {
        var social_meta = document.getElementById(`social_meta_${id}`).value;
    }

    // save input with index==id
    saveInputArray.forEach((input) => {
        if(input.index === id) {
            input.index = id;
            input.social_id = social_id;
            input.social_name = social_name;
            input.social_image = social_image;
            input.social_meta = social_meta;
            console.log("Modification des valeurs de l'input");
            edited = true;
        }
    })

    // if not edited create one
    if(!edited) {
        saveInputArray.push({ 
                    "index": id,
                    "id": social_id,
                    "social_name" : social_name,
                    "social_image": social_image,
                    "social_meta": social_meta
                    });
        console.log("Ajout des valeurs de l'input dans le tableau");      
    }
}

function restoreInput() {
    console.log(`Restauration des valeurs des inp`);
    saveInputArray.forEach(input => {
        if(document.getElementById(`social_id_${input.index}`)) {
            document.getElementById(`social_id_${input.index}`).value = input.id;
        }
        if(document.getElementById(`social_name_${input.index}`)) {
            document.getElementById(`social_name_${input.index}`).value = input.social_name;
        }
        if(document.getElementById(`social_image_${input.index}`)) {
            document.getElementById(`social_image_${input.index}`).value = input.social_image;
        }
        if(document.getElementById(`social_meta_${input.index}`)) {
            document.getElementById(`social_meta_${input.index}`).value = input.social_meta;
        }
    });
}

function deleteSocial(id) {
    document.getElementById(`social-${id}`).outerHTML="";
}
