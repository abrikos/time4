CREATE TABLE "card" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT,
    "number" TEXT,
    "percent" INTEGER DEFAULT (5),
    "bonus" INTEGER DEFAULT (0),
    "delta" INTEGER DEFAULT (0)
);


CREATE TABLE "bonus"(
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "card" INTEGER,
  "date" INTEGER,
  "price" INTEGER(0),
  "haircut" INTEGER,
  "status" INTEGER (1)
);
CREATE INDEX "haircut_idx" on bonus (haircut ASC);

ALTER table "haircut"  ADD  "bonus_id" INTEGER;
CREATE INDEX "haircut_bonus_id_idx" on haircut (bonus_id ASC);

ALTER table "haircut"  ADD  "discount" INTEGER;

ALTER table "haircut"  ADD "card_id" INTEGER;
CREATE INDEX "haircut_card_id_idx" on haircut (card_id ASC);

