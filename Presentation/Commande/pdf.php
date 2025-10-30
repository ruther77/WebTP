<?php
    require_once __DIR__ . '/../../config/app.php';
    require_once __DIR__ . '/../../DAO/DAO.php';
    require_once __DIR__ . '/../../Metier/client.php';
    require_once __DIR__ . '/../../Metier/produit.php';
    require_once __DIR__ . '/../../Metier/commande.php';
    require_once __DIR__ . '/../../Metier/ligneCmd.php';

    $commandeId = filter_input(INPUT_GET, 'ref', FILTER_VALIDATE_INT);
    if (!$commandeId) {
        exit('Commande introuvable');
    }

    $commande = Commande::getCommande($commandeId);
    if (!$commande) {
        exit('Commande introuvable');
    }

    $dao = new DAO();
    $lignes = LigneCmd::afficher($commande->get('n'));
    $client = $dao->getClient($commande->get('i'));
    $totalCommande = LigneCmd::total($commande->get('n'));
    $totalCommandeFormatted = number_format((float) $totalCommande, 2, '.', ' ');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="pdf.css">
        <link rel="stylesheet" href="pdfboot.css">
        <title>Document</title>
    </head>
    <body >


    <div class="card-body" style="width:360px; border:1px black solid">
        <div class="form-group row text-left mb-0">
            <div style="display: flex; justify-content: center; margin:0 auto 15px auto; opacity:0.94;">
                <img width="110" src="<?= asset('assets/images/logo/logo-jell.png'); ?>" alt="" class="logo logo-lg align-self-center">
            </div>
            
            <div class="col-sm-9 py-1">
                <h6>Date: <?= htmlspecialchars($commande->get('d'), ENT_QUOTES, 'UTF-8'); ?> </h6>
            </div>
        </div>
        <h5>---------------------------------------</h5>
        <div class="form-group row text-left mb-0 py-2">
            <div class="col-sm-7 py-1">
                <h6 class="font-weight-bold"><?= htmlspecialchars($client ? $client->get('n') : 'Client inconnu', ENT_QUOTES, 'UTF-8'); ?></h6>
                <div class="mb-1">Tél: <?= $client ? htmlspecialchars('0' . $client->get('t'), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></div>
                <div class="mb-1"> Email: <?= $client ? htmlspecialchars($client->get('e'), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></div>
                <div class="mb-1"> Adresse: <?= $client ? htmlspecialchars($client->get('a'), ENT_QUOTES, 'UTF-8') : 'N/A'; ?></div>
            </div>
        <div style="text-align: center;" class="col-sm-4 py-1">
             <h6>Commande #<?= htmlspecialchars($commande->get('n'), ENT_QUOTES, 'UTF-8'); ?> </h6>
        </div>
        </div>
        <table class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                <th>Produits</th>
                <th width="8%">Qty</th>
                <th width="20%">Prix</th>
                <th width="20%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php                                                       
             
                    foreach($lignes as $l) {
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($l['libelle'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars((string) $l['quantite'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars((string) $l['prixVente'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars((string) $l['total'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php } 
                
                ?>         
            </tbody>
        </table>
        <div class="form-group row text-left mb-0 py-2">
            <div class="col-sm-4 py-1"></div>
            <div class="col-sm-8 py-1">
                <h6>-------------------------------</h6>
                <div class="d-flex justify-content-between">
                    <h5 class="font-weight-bold">TOTAL :</h5>
                    <h5 class="text-right font-weight-bold"><?= htmlspecialchars($totalCommandeFormatted, ENT_QUOTES, 'UTF-8'); ?> Dh</h5>
                </div>
                <table width="100%">
                    <tbody>
                            <tr>
                                <td class="">Prix HT</td>
                                <td class="text-right"><?= htmlspecialchars($totalCommandeFormatted, ENT_QUOTES, 'UTF-8'); ?>-20 Dh</td>
                            </tr>
                            <tr>
                                <td class="">Taxe</td>
                                <td class="text-right">20 Dh</td>
                            </tr>
                        </tbody>
                    </table>
                    <h6>-------------------------------</h6>
            </div>    
        </div>
        <h5>---------------------------------------</h5> 
        <div class="row justify-content-center">
            <h5>JELLOULI ste.</h5>
            <p>Rue 18, imm 52, Salmia 2, Casablanca</p>
            <h5>**MERCI POUR VOTRE VISITE**</h5>  
        </div>  
    </div>
    <script>
        window.addEventListener('afterprint', (event) => {
            window.close();
            
        });
    </script>
        
    

    </body>
    </html>