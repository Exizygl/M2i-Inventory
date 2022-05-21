
$(document).ready(function () {


    

    $("#recherche").keyup(function () {
            
            var recherche = $(this).val();
            var typeListe = $(".typeListe:checked").val();
            var typeMateriel = $(".typeMateriel:checked").val();
            var typeMateriel = $(".typeMateriel:checked").val();
            var numberRam = $(".numberRam:checked").val();
            var typeProcesseur = $(".typeProcesseur:checked").val();
            var typeDisqueDur = $(".typeDisqueDur:checked").val();
            var typeModele = $(".typeModele:checked").val();
            var typeComposant = $(".typeComposant:checked").val();
            var typeCatalogue = $(".typeCatalogue:checked").val();
            var typePeripherique = $(".typePeripherique:checked").val();
            var typeVille = $(".typeVille:checked").val();

            $.ajax({
                url: "recherche.php",
                method: "POST",
                data: {
                    recherche: recherche,
                    typeListe: typeListe,
                    typeMateriel: typeMateriel,
                    numberRam: numberRam,
                    typeProcesseur: typeProcesseur,
                    typeDisqueDur: typeDisqueDur,
                    typeModele: typeModele,
                    typeComposant: typeComposant,
                    typeCatalogue: typeCatalogue,
                    typePeripherique: typePeripherique,
                    typeVille: typeVille
                },

                success: function (data) {
                    $("#liste").html(data);
                }

            });
        }
    );

    $(".typeListe, .filter").click(function () {
        
        var recherche = $("#recherche").val();
        var typeListe = $(".typeListe:checked").val();
        var typeMateriel = $(".typeMateriel:checked").val();
        var numberRam = $(".numberRam:checked").val();
        var typeProcesseur = $(".typeProcesseur:checked").val();
        var typeDisqueDur = $(".typeDisqueDur:checked").val();
        var typeModele = $(".typeModele:checked").val();
        var typeComposant = $(".typeComposant:checked").val();
        var typeCatalogue = $(".typeCatalogue:checked").val();
        var typePeripherique = $(".typePeripherique:checked").val();
        var typeVille = $(".typeVille:checked").val();

        $.ajax({
            url: "recherche.php",
            method: "POST",
            data: {
                recherche: recherche,
                typeListe: typeListe,
                typeMateriel: typeMateriel,
                numberRam: numberRam,
                typeProcesseur: typeProcesseur,
                typeDisqueDur: typeDisqueDur,
                typeModele: typeModele,
                typeComposant: typeComposant,
                typeCatalogue: typeCatalogue,
                typePeripherique: typePeripherique,
                typeVille: typeVille
            },

            success: function (data) {
                $("#liste").html(data);
            }
        });

    })

    // Changement du formulaire
    $(".typeListe, .filter").click(function () {

        
        var typeListe = $(".typeListe:checked").val();
        var typeMateriel = $(".typeMateriel:checked").val();
        var numberRam = $(".numberRam:checked").val();
        var typeProcesseur = $(".typeProcesseur:checked").val();
        var typeDisqueDur = $(".typeDisqueDur:checked").val();
        var typeModele = $(".typeModele:checked").val();
        var typeComposant = $(".typeComposant:checked").val();
        var typeCatalogue = $(".typeCatalogue:checked").val();
        var typePeripherique = $(".typePeripherique:checked").val();
        var typeVille = $(".typeVille:checked").val();

        $.ajax({
            url: "filter.php",
            method: "POST",
            data: {
                typeListe: typeListe,
                typeMateriel: typeMateriel,
                numberRam: numberRam,
                typeProcesseur: typeProcesseur,
                typeDisqueDur: typeDisqueDur,
                typeModele: typeModele,
                typeComposant: typeComposant,
                typeCatalogue: typeCatalogue,
                typePeripherique: typePeripherique,
                typeVille: typeVille
            },

            success: function (data) {
                $("#filterDetails").html(data);
            }
        });

    })
});

