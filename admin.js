
    function showSection(sectionId) {
        const sections = ['dashboard', 'offres', 'projets', 'utilisateurs', 'freelancers', 'categorier', 'sous_categories' , 'Temoignages'];
        sections.forEach(section => {
        document.getElementById(section).classList.add('hidden');
        });
        document.getElementById(sectionId).classList.remove('hidden');
    }

    function showUpdateForm(id, etat) {
        document.getElementById('update_id_projet').value = id;
        document.getElementById('update_etat').value = etat; 
        document.getElementById('updateForm').classList.remove('hidden');
    }

    function hideUpdateForm() {
        document.getElementById('updateForm').classList.add('hidden');
    }

    

    function showUpdateCategoryForm(id, nom) {
        document.getElementById('update_id_categorie').value = id;
        document.getElementById('update_nom_categorie').value = nom;
        document.getElementById('updateCategoryForm').classList.remove('hidden');
    }

    function hideUpdateCategoryForm() {
        document.getElementById('updateCategoryForm').classList.add('hidden');
    }

    function showAddCategoryForm() {
        document.getElementById('addCategoryForm').classList.remove('hidden');
    }

    function hideAddCategoryForm() {
        document.getElementById('addCategoryForm').classList.add('hidden');
    }

    function showUpdateSousCategoryForm(id, nom, id_categorie) {
        document.getElementById('update_id_sous_categorie').value = id;
        document.getElementById('update_nom_sous_categorie').value = nom;
        document.getElementById('update_id_categorie').value = id_categorie;
        document.getElementById('updateSousCategoryForm').classList.remove('hidden');
    }

    function hideUpdateSousCategoryForm() {
        document.getElementById('updateSousCategoryForm').classList.add('hidden');
    }

    function showAddSousCategoryForm() {
        document.getElementById('addSousCategoryForm').classList.remove('hidden');
    }

    function hideAddSousCategoryForm() {
        document.getElementById('addSousCategoryForm').classList.add('hidden');
    }
