<?php

declare(strict_types=1);

class DAO
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = $this->createConnection();
    }

    private function createConnection(): \PDO
    {
        static $config;
        if ($config === null) {
            $config = require __DIR__ . '/../config/database.php';
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['dbname'],
            $config['charset']
        );

        return new \PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    private function getPDO(): \PDO
    {
        return $this->pdo;
    }

    public function authentification(string $login, string $password): ?array
    {
        $stmt = $this->getPDO()->prepare(
            'SELECT * FROM administrateurs WHERE login = ? AND password = ?'
        );
        $stmt->execute([$login, $password]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function executeQuery(string $sql, array $params = []): array
    {
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // CLIENT -----------------------------------------------------------------

    public function ajouterClient(Client $client): void
    {
        $this->getPDO()
            ->prepare('INSERT INTO client(nom, adresse, telephone, email) VALUES(?, ?, ?, ?)')
            ->execute([
                $client->get('n'),
                $client->get('a'),
                $client->get('t'),
                $client->get('e'),
            ]);
    }

    public function afficherClient(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM client');
        $clients = [];

        while ($row = $stmt->fetch()) {
            $cli = new Client($row['nom'], $row['adresse'], $row['telephone'], $row['email']);
            $cli->setId((int) $row['idClient']);
            $clients[] = $cli;
        }

        return $clients;
    }

    public function getClient(int $id): ?Client
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM client WHERE idClient = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $client = new Client($row['nom'], $row['adresse'], $row['telephone'], $row['email']);
        $client->setId((int) $row['idClient']);

        return $client;
    }

    public function getClientTotal(): int
    {
        $stmt = $this->getPDO()->query('SELECT COUNT(*) AS number FROM client');
        $row = $stmt->fetch();

        return $row ? (int) $row['number'] : 0;
    }

    public function updateClient(Client $client): void
    {
        $this->getPDO()
            ->prepare('UPDATE client SET nom = ?, adresse = ?, telephone = ?, email = ? WHERE idClient = ?')
            ->execute([
                $client->get('n'),
                $client->get('a'),
                $client->get('t'),
                $client->get('e'),
                $client->get('i'),
            ]);
    }

    public function deleteClient(int $id): void
    {
        $this->getPDO()->prepare('DELETE FROM client WHERE idClient = ?')->execute([$id]);
    }

    // FOURNISSEUR -------------------------------------------------------------

    public function ajouterFournisseur(Fournisseur $fournisseur): void
    {
        $this->getPDO()
            ->prepare('INSERT INTO fournisseur(nom, adresse, telephone, email) VALUES(?, ?, ?, ?)')
            ->execute([
                $fournisseur->get('n'),
                $fournisseur->get('a'),
                $fournisseur->get('t'),
                $fournisseur->get('e'),
            ]);
    }

    public function afficherFournisseur(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM fournisseur');
        $fournisseurs = [];

        while ($row = $stmt->fetch()) {
            $fournisseur = new Fournisseur($row['nom'], $row['adresse'], $row['telephone'], $row['email']);
            $fournisseur->setId((int) $row['idFournisseur']);
            $fournisseurs[] = $fournisseur;
        }

        return $fournisseurs;
    }

    public function getFournisseur(int $id): ?Fournisseur
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM fournisseur WHERE idFournisseur = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $fournisseur = new Fournisseur($row['nom'], $row['adresse'], $row['telephone'], $row['email']);
        $fournisseur->setId((int) $row['idFournisseur']);

        return $fournisseur;
    }

    public function updateFournisseur(Fournisseur $fournisseur): void
    {
        $this->getPDO()
            ->prepare('UPDATE fournisseur SET nom = ?, adresse = ?, telephone = ?, email = ? WHERE idFournisseur = ?')
            ->execute([
                $fournisseur->get('n'),
                $fournisseur->get('a'),
                $fournisseur->get('t'),
                $fournisseur->get('e'),
                $fournisseur->get('i'),
            ]);
    }

    public function deleteFournisseur(int $id): void
    {
        $this->getPDO()->prepare('DELETE FROM fournisseur WHERE idFournisseur = ?')->execute([$id]);
    }

    // PRODUIT -----------------------------------------------------------------

    public function ajouterProduit(Produit $produit): void
    {
        $this->getPDO()
            ->prepare(
                'INSERT INTO produit(reference, libelle, prixUnitaire, quantiteStock, prixAchat, image, idCategorie) '
                . 'VALUES(?, ?, ?, ?, ?, ?, ?)'
            )
            ->execute([
                $produit->get('r'),
                $produit->get('l'),
                $produit->get('p'),
                $produit->get('q'),
                $produit->get('a'),
                $produit->get('i'),
                $produit->get('c'),
            ]);
    }

    public function afficherProduits(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM produit NATURAL JOIN categorie');
        $produits = [];

        while ($row = $stmt->fetch()) {
            $produits[] = new Produit(
                $row['reference'],
                $row['libelle'],
                (float) $row['prixUnitaire'],
                (int) $row['quantiteStock'],
                (float) $row['prixAchat'],
                $row['image'],
                $row['nomCategorie']
            );
        }

        return $produits;
    }

    public static function afficherProduitsByCat(int $categorieId): array
    {
        $dao = new self();
        $stmt = $dao->getPDO()->prepare('SELECT * FROM produit NATURAL JOIN categorie WHERE idCategorie = ?');
        $stmt->execute([$categorieId]);
        $produits = [];

        while ($row = $stmt->fetch()) {
            $produits[] = new Produit(
                $row['reference'],
                $row['libelle'],
                (float) $row['prixUnitaire'],
                (int) $row['quantiteStock'],
                (float) $row['prixAchat'],
                $row['image'],
                $row['nomCategorie']
            );
        }

        return $produits;
    }

    public function getTrendingProducts(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $stmt = $this->getPDO()->prepare(
            'SELECT lignecmd.reference '
            . 'FROM commande '
            . 'JOIN lignecmd ON lignecmd.numeroCmd = commande.numeroCmd '
            . 'GROUP BY lignecmd.reference '
            . 'ORDER BY SUM(lignecmd.quantite) DESC, MAX(commande.date) DESC '
            . 'LIMIT ?'
        );

        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $products = [];

        while ($row = $stmt->fetch()) {
            $product = $this->getProduit($row['reference']);

            if ($product !== null) {
                $products[] = $product;
            }
        }

        if (!$products) {
            $allProducts = $this->afficherProduits();
            $products = array_slice($allProducts, 0, $limit);
        }

        return $products;
    }

    public function getProduit(string $reference): ?Produit
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM produit WHERE reference = ?');
        $stmt->execute([$reference]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Produit(
            $row['reference'],
            $row['libelle'],
            (float) $row['prixUnitaire'],
            (int) $row['quantiteStock'],
            (float) $row['prixAchat'],
            $row['image'],
            $row['idCategorie']
        );
    }

    public function updateProduit(Produit $produit): void
    {
        $this->getPDO()
            ->prepare(
                'UPDATE produit SET libelle = ?, prixUnitaire = ?, quantiteStock = ?, prixAchat = ?, image = ?, idCategorie = ? '
                . 'WHERE reference = ?'
            )
            ->execute([
                $produit->get('l'),
                $produit->get('p'),
                $produit->get('q'),
                $produit->get('a'),
                $produit->get('i'),
                $produit->get('c'),
                $produit->get('r'),
            ]);
    }

    public function deleteProduit(string $reference): void
    {
        $this->getPDO()->prepare('DELETE FROM produit WHERE reference = ?')->execute([$reference]);
    }

    // COMMANDE ----------------------------------------------------------------

    public function ajouterCommande(Commande $commande): void
    {
        $this->getPDO()
            ->prepare('INSERT INTO commande(date, idClient) VALUES(?, ?)')
            ->execute([
                $commande->get('d'),
                $commande->get('i'),
            ]);
    }

    public function afficherCommande(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM commande');
        $commandes = [];

        while ($row = $stmt->fetch()) {
            $commandes[] = new Commande($row['numeroCmd'], $row['date'], (int) $row['idClient']);
        }

        return $commandes;
    }

    public function getCommande(int $id): ?Commande
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM commande WHERE numeroCmd = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Commande($row['numeroCmd'], $row['date'], (int) $row['idClient']);
    }

    public function getCommandeTotal(): int
    {
        $stmt = $this->getPDO()->query('SELECT COUNT(*) AS number FROM commande');
        $row = $stmt->fetch();

        return $row ? (int) $row['number'] : 0;
    }

    public static function getCommandeId(string $date, int $idClient): ?int
    {
        $dao = new self();
        $stmt = $dao->getPDO()->prepare('SELECT numeroCmd FROM commande WHERE date = ? AND idClient = ?');
        $stmt->execute([$date, $idClient]);
        $row = $stmt->fetch();

        return $row ? (int) $row['numeroCmd'] : null;
    }

    public function deleteCommande(int $id): void
    {
        $pdo = $this->getPDO();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('DELETE FROM lignecmd WHERE numeroCmd = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM commande WHERE numeroCmd = ?')->execute([$id]);
            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    public static function Income(): ?float
    {
        $dao = new self();
        $stmt = $dao->getPDO()->query('SELECT SUM(prixVente) AS prix FROM commande NATURAL JOIN lignecmd');
        $row = $stmt->fetch();

        return $row && $row['prix'] !== null ? (float) $row['prix'] : null;
    }

    public function createCommande(string $date, int $idClient, array $items): int
    {
        if ($idClient <= 0) {
            throw new \InvalidArgumentException('Identifiant client invalide');
        }

        if (empty($items)) {
            throw new \InvalidArgumentException('La commande doit contenir au moins un produit');
        }

        $pdo = $this->getPDO();

        $pdo->beginTransaction();

        try {
            $pdo->prepare('INSERT INTO commande(date, idClient) VALUES(?, ?)')->execute([$date, $idClient]);
            $commandeId = (int) $pdo->lastInsertId();

            $selectStock = $pdo->prepare('SELECT quantiteStock FROM produit WHERE reference = ? FOR UPDATE');
            $insertLine = $pdo->prepare('INSERT INTO lignecmd VALUES(?, ?, ?, ?)');
            $updateStock = $pdo->prepare('UPDATE produit SET quantiteStock = quantiteStock - ? WHERE reference = ?');

            foreach ($items as $item) {
                $reference = $item['reference'] ?? null;
                $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;
                $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : null;

                if (!$reference || $quantity <= 0 || $unitPrice === null) {
                    throw new \InvalidArgumentException('Ligne de commande invalide');
                }

                $selectStock->execute([$reference]);
                $stockRow = $selectStock->fetch();

                if (!$stockRow) {
                    throw new \RuntimeException(sprintf('Produit %s introuvable', $reference));
                }

                if ((int) $stockRow['quantiteStock'] < $quantity) {
                    throw new \RuntimeException(sprintf('Stock insuffisant pour le produit %s', $reference));
                }

                $insertLine->execute([$commandeId, $quantity, $reference, $unitPrice]);
                $updateStock->execute([$quantity, $reference]);
            }

            $pdo->commit();

            return $commandeId;
        } catch (\Throwable $exception) {
            $pdo->rollBack();

            throw $exception;
        }
    }

    public function createApprovisionnement(string $date, int $idFournisseur, array $items): int
    {
        if ($idFournisseur <= 0) {
            throw new \InvalidArgumentException('Identifiant fournisseur invalide');
        }

        if (empty($items)) {
            throw new \InvalidArgumentException('L\'approvisionnement doit contenir au moins un produit');
        }

        $pdo = $this->getPDO();

        $pdo->beginTransaction();

        try {
            $pdo->prepare('INSERT INTO approvisionnement(date, idFournisseur) VALUES(?, ?)')
                ->execute([$date, $idFournisseur]);

            $approId = (int) $pdo->lastInsertId();

            $insertLine = $pdo->prepare('INSERT INTO ligneappro VALUES(?, ?, ?, ?)');
            $updateStock = $pdo->prepare('UPDATE produit SET quantiteStock = quantiteStock + ? WHERE reference = ?');

            foreach ($items as $item) {
                $reference = $item['reference'] ?? null;
                $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;
                $purchasePrice = isset($item['purchase_price']) ? (float) $item['purchase_price'] : null;

                if (!$reference || $quantity <= 0 || $purchasePrice === null) {
                    throw new \InvalidArgumentException('Ligne d\'approvisionnement invalide');
                }

                $insertLine->execute([$approId, $quantity, $reference, $purchasePrice]);
                $updateStock->execute([$quantity, $reference]);
            }

            $pdo->commit();

            return $approId;
        } catch (\Throwable $exception) {
            $pdo->rollBack();

            throw $exception;
        }
    }

    // APPROVISIONNEMENT -------------------------------------------------------

    public function ajouterApprovis(Approvis $approvisionnement): void
    {
        $this->getPDO()
            ->prepare('INSERT INTO approvisionnement(date, idFournisseur) VALUES(?, ?)')
            ->execute([
                $approvisionnement->get('d'),
                $approvisionnement->get('i'),
            ]);
    }

    public function afficherApprovis(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM approvisionnement');
        $approvisionnements = [];

        while ($row = $stmt->fetch()) {
            $approvisionnements[] = new Approvis($row['numeroAppro'], $row['date'], (int) $row['idFournisseur']);
        }

        return $approvisionnements;
    }

    public function getApprovis(int $id): ?Approvis
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM approvisionnement WHERE numeroAppro = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Approvis($row['numeroAppro'], $row['date'], (int) $row['idFournisseur']);
    }

    public function getApprovisTotal(): int
    {
        $stmt = $this->getPDO()->query('SELECT COUNT(*) AS number FROM approvisionnement');
        $row = $stmt->fetch();

        return $row ? (int) $row['number'] : 0;
    }

    public static function getApprovisId(string $date, int $idFournisseur): ?int
    {
        $dao = new self();
        $stmt = $dao->getPDO()->prepare('SELECT numeroAppro FROM approvisionnement WHERE date = ? AND idFournisseur = ?');
        $stmt->execute([$date, $idFournisseur]);
        $row = $stmt->fetch();

        return $row ? (int) $row['numeroAppro'] : null;
    }

    public function deleteApprovis(int $id): void
    {
        $pdo = $this->getPDO();
        $pdo->beginTransaction();

        try {
            $pdo->prepare('DELETE FROM ligneappro WHERE numeroAppro = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM approvisionnement WHERE numeroAppro = ?')->execute([$id]);
            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }

    // CATEGORIE ---------------------------------------------------------------

    public function ajouterCategorie(Categorie $categorie): void
    {
        $this->getPDO()
            ->prepare('INSERT INTO categorie(idCategorie, nomCategorie) VALUES(?, ?)')
            ->execute([
                $categorie->get('i'),
                $categorie->get('n'),
            ]);
    }

    public function afficherCategorie(): array
    {
        $stmt = $this->getPDO()->query('SELECT * FROM categorie');
        $categories = [];

        while ($row = $stmt->fetch()) {
            $categories[] = new Categorie($row['idCategorie'], $row['nomCategorie']);
        }

        return $categories;
    }

    public function getCategorie(int $id): ?Categorie
    {
        $stmt = $this->getPDO()->prepare('SELECT * FROM categorie WHERE idCategorie = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Categorie($row['idCategorie'], $row['nomCategorie']);
    }

    public function updateCategorie(Categorie $categorie): void
    {
        $this->getPDO()
            ->prepare('UPDATE categorie SET nomCategorie = ? WHERE idCategorie = ?')
            ->execute([
                $categorie->get('n'),
                $categorie->get('i'),
            ]);
    }

    public function deleteCategorie(int $id): void
    {
        $this->getPDO()->prepare('DELETE FROM categorie WHERE idCategorie = ?')->execute([$id]);
    }

    // LIGNE CMD ---------------------------------------------------------------

    public function ajouterLigneCmd(LigneCmd $ligne): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare('INSERT INTO lignecmd VALUES(?, ?, ?, ?)')->execute([
            $ligne->get('n'),
            $ligne->get('q'),
            $ligne->get('r'),
            $ligne->get('p'),
        ]);

        $pdo->prepare('UPDATE produit SET quantiteStock = ? WHERE reference = ?')->execute([
            $ligne->get('i'),
            $ligne->get('r'),
        ]);
    }

    public function afficherLigneCmd(int $commandeId): array
    {
        return $this->executeQuery(
            'SELECT reference, libelle, quantite, prixVente, (quantite * prixVente) AS total '
            . 'FROM lignecmd NATURAL JOIN produit WHERE numeroCmd = ?',
            [$commandeId]
        );
    }

    public function totalCmd(int $commandeId): float
    {
        $stmt = $this->getPDO()->prepare('SELECT SUM(prixVente * quantite) AS total FROM lignecmd WHERE numeroCmd = ?');
        $stmt->execute([$commandeId]);
        $row = $stmt->fetch();

        return $row && $row['total'] !== null ? (float) $row['total'] : 0.0;
    }

    // LIGNE APPRO -------------------------------------------------------------

    public function ajouterLigneAppro(LigneAppro $ligne): void
    {
        $pdo = $this->getPDO();
        $pdo->prepare('INSERT INTO ligneappro VALUES(?, ?, ?, ?)')->execute([
            $ligne->get('n'),
            $ligne->get('q'),
            $ligne->get('r'),
            $ligne->get('p'),
        ]);

        $pdo->prepare('UPDATE produit SET quantiteStock = ? WHERE reference = ?')->execute([
            $ligne->get('i'),
            $ligne->get('r'),
        ]);
    }

    public function afficherLigneAppro(int $approId): array
    {
        return $this->executeQuery(
            'SELECT produit.reference, libelle, quantite, ligneappro.prixAchat, '
            . '(quantite * ligneappro.prixAchat) AS total '
            . 'FROM ligneappro JOIN produit ON produit.reference = ligneappro.reference '
            . 'WHERE numeroAppro = ?',
            [$approId]
        );
    }

    public function totalAppro(int $approId): float
    {
        $stmt = $this->getPDO()->prepare('SELECT SUM(prixAchat * quantite) AS total FROM ligneappro WHERE numeroAppro = ?');
        $stmt->execute([$approId]);
        $row = $stmt->fetch();

        return $row && $row['total'] !== null ? (float) $row['total'] : 0.0;
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        $months = max(1, $months);

        $stmt = $this->getPDO()->prepare(
            'SELECT DATE_FORMAT(date, \'%Y-%m\') AS month, '
            . 'SUM(lignecmd.prixVente * lignecmd.quantite) AS total '
            . 'FROM commande '
            . 'JOIN lignecmd ON lignecmd.numeroCmd = commande.numeroCmd '
            . 'GROUP BY DATE_FORMAT(date, \'%Y-%m\') '
            . 'ORDER BY DATE_FORMAT(date, \'%Y-%m\') DESC '
            . 'LIMIT ?'
        );

        $stmt->bindValue(1, $months, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = array_reverse($stmt->fetchAll());

        return array_map(
            static fn (array $row): array => [
                'month' => $row['month'],
                'total' => $row['total'] !== null ? (float) $row['total'] : 0.0,
            ],
            $rows
        );
    }
}
