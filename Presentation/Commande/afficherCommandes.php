    <!-- ----------------------------------------------------------------------------------------- -->
    <!--                                          Header                                           -->
    <!-- ----------------------------------------------------------------------------------------- -->

    <?php $title = "Commandes" ;include "../templates/header.php" ?>

    <!-- ----------------------------------------------------------------------------------------- -->
    <!--                                          Container                                        -->
    <!-- ----------------------------------------------------------------------------------------- -->

    <div id="main">
        <br>
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Commandes</h3>
                        <!-- <p class="text-subtitle text-muted">Ajout d'un client </p> -->
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Commandes</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">



                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Affichage des Commandes</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="email-fixed-search flex-grow-1">
                                    <div class="form-group position-relative mb-0 has-icon-left">
                                        <input type="text" class="form-control" placeholder="Rechercher le nom..."
                                            id="search" onkeyup="FilterkeyWord()">
                                        <div class="form-control-icon">
                                            <svg class="bi" width="1.5em" height="1.5em" fill="currentColor">
                                                <use xlink:href="<?= asset('assets/images/bootstrap-icons.svg'); ?>#search"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-md table-striped" id="table">
                                        <thead>
                                            <tr>
                                                <th>Numero</th>
                                                <th>Date</th>
                                                <th>Client</th>
                                                <th>Action</th>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <?php
                                            $commandes = Commande::afficher();
                                            $dao = new DAO();
                                            foreach ($commandes as $commande) {
                                                $client = $dao->getClient($commande->get('i'));
                                                $clientName = $client ? $client->get('n') : 'Client indisponible';
                                                $commandeId = htmlspecialchars($commande->get('n'), ENT_QUOTES, 'UTF-8');
                                                $commandeDate = htmlspecialchars($commande->get('d'), ENT_QUOTES, 'UTF-8');
                                                $clientLabel = htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8');
                                                $modalId = 'view' . $commandeId;
                                                $deleteId = 'supprimer' . $commandeId;
                                                $totalCommande = LigneCmd::total($commande->get('n'));
                                                $totalCommandeFormatted = number_format((float) $totalCommande, 2, '.', ' ');
                                                ?>
                                            <tr>
                                                <td><?= $commandeId; ?></td>
                                                <td><?= $commandeDate; ?></td>
                                                <td><?= $clientLabel; ?></td>
                                                <td>
                                                    <span>
                                                        <a data-bs-toggle="modal" data-bs-target="#<?= $modalId; ?>">
                                                            <svg class="bi" width="1em" height="1em" fill="currentColor">
                                                                <use xlink:href="<?= asset('assets/images/bootstrap-icons.svg'); ?>#eye"></use>
                                                            </svg>
                                                        </a>&#124;
                                                        <a data-bs-toggle="modal" data-bs-target="#<?= $deleteId; ?>">
                                                            <svg class="bi" width="1em" height="1em" fill="currentColor">
                                                                <use xlink:href="<?= asset('assets/images/bootstrap-icons.svg'); ?>#trash"></use>
                                                            </svg>
                                                        </a>

                                                    </span>


                                                    <div class="modal fade bd-example-modal-lg"
                                                        id="<?= $modalId; ?>" tabindex="0" role="dialog"
                                                        aria-labelledby="modalLabel<?= $commandeId; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="modalLabel<?= $commandeId; ?>">Commande</h4>
                                                                    <button type="button" class="close"
                                                                        data-bs-dismiss="modal">&times;
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" id="<?= $commandeId; ?>">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-md " id="table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Reference</th>
                                                                                    <th>Libelle</th>
                                                                                    <th>Quantite</th>
                                                                                    <th>Prix de Vente</th>
                                                                                </tr>

                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                $lines = LigneCmd::afficher($commande->get('n'));
                                                                                foreach ($lines as $line) {
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?= htmlspecialchars($line['reference'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                                    <td><?= htmlspecialchars($line['libelle'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                                    <td><?= htmlspecialchars((string) $line['quantite'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                                    <td><?= htmlspecialchars((string) $line['prixVente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                                                </tr>
                                                                                <?php }

                                                                            ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    <div class="card-text font-bold d-flex justify-content-between"
                                                                        style="margin: 0 2rem">
                                                                        <div></div>
                                                                        <div>Total : <?= htmlspecialchars($totalCommandeFormatted, ENT_QUOTES, 'UTF-8'); ?>
                                                                            Dhs
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="button" class="btn btn-success"
                                                                       onclick="printCommande('<?= $commandeId; ?>')">Print</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- ---------------------------Supprimer --------------------------- -->
                                                    <div class="modal fade bd-example-modal-sm"
                                                        id="<?= $deleteId; ?>" tabindex="0" role="dialog"
                                                        aria-labelledby="deleteLabel<?= $commandeId; ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="deleteLabel<?= $commandeId; ?>">Alert
                                                                    </h4>
                                                                    <button type="button" class="close"
                                                                        data-bs-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Vous êtes sûr de supprimer cette commande ?</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <a
                                                                        href="<?= url_for('Presentation/Commande/supprimerCommande.php'); ?>?id=<?= $commandeId; ?>">
                                                                        <button type="button"
                                                                            class="btn btn-primary">Oui !
                                                                            Supprimer</button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </tr>
                                            <?php }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <button type=" button" class="btn btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#mymodal">Small modal</button> -->





                </div>
            </section>
        </div>

        <!-- ----------------------------------------------------------------------------------------- -->
        <!--                                          Footer                                          -->
        <!-- ----------------------------------------------------------------------------------------- -->

        <?php include "../templates/footer.php" ?>
        <script>
            const commandePdfUrl = <?= json_encode(url_for('Presentation/Commande/pdf.php')); ?>;

            function printCommande(ref){
                const targetUrl = `${commandePdfUrl}?ref=${encodeURIComponent(ref)}`;
                const mywindow = window.open(targetUrl, 'PRINT', 'height=400,width=600');
                if (!mywindow) {
                    return;
                }
                mywindow.focus();
                mywindow.print();
            }
        </script>