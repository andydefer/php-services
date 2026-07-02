# Services

| Service | Description | Documentation |
|---------|-------------|---------------|
| FileSystemService | Opérations natives sur le système de fichiers (lecture, écriture, copie, déplacement, suppression) sans dépendances externes | [FileSystemService](services/filesystem-service.md) |
| PrimitiveTypeConverterService | Conversion entre types primitifs PHP (bool, string, int, float, null) avec détection de type et valeurs par défaut | [PrimitiveTypeConverterService](services/primitive-type-converter-service.md) |
| TextNormalizerService | Normalisation et extraction de mots pour le traitement de texte (suppression accents, émojis, symboles monétaires, stop-words, etc.) | [TextNormalizerService](services/text-normalizer-service.md) |
| UniqueExtractorService | Extraction des lettres uniques et mots uniques d'un texte avec ou sans normalisation, recherche par préfixe et analyse de fréquence | [UniqueExtractorService](services/unique-extractor-service.md) |
| NGramGeneratorService | Génération de n-grammes (bigrammes, trigrammes, quadrigrammes, etc.) avec plage de tailles configurable et support de normalisation | [NGramGeneratorService](services/ngram-generator-service.md) |
| WordVectorGeneratorService | Génération de vecteurs numériques pour les mots via hachage de n-grammes, avec calcul de similarité cosinus entre vecteurs | [WordVectorGeneratorService](services/word-vector-generator-service.md) |