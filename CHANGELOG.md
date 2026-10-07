# Changelog

Cambios del SDK y notas de comportamiento del API de FiscalAPI que afectan a quien usa el SDK.

## [Sin publicar]

### Personas (`getPersonService()`)

El SDK envía y recibe las personas como arreglos, así que estos cambios del contrato no cambian código:

- El API no tiene `capitalRegime`: lo ignora y no lo devuelve. Envíe `legalName` sin régimen de capital.
- La respuesta incluye `phoneNumber`, `countryId`, `country`, `foreignTin`, `balances` (saldos por tipo de crédito: `creditType` y `available`; solo aparecen los tipos que la persona ha tenido) e `isOwner` (`true` si la persona es el owner de su tenant).
- `password`: requerida al crear; al actualizar, omitida, `null` o vacía conserva la contraseña actual. El API nunca la devuelve.
- `userTypeId`: `'C'` (cliente, el valor por omisión al crear) o `'U'` (usuario). `'T'` (tenant) solo llega en respuestas: el API lo rechaza al crear y al actualizar solo lo acepta si la persona ya es `'T'`.
- `taxPassword` es la contraseña de la llave privada (.key) que la persona guarda en su perfil; el API no la usa para sellar (al timbrar usa la contraseña de los certificados registrados o la de `taxCredentials`). Solo la reciben con valor la propia persona y el owner del tenant; los demás reciben `null`. Al actualizar, `null` la conserva y `''` la borra.
- El API ya no devuelve `twoFactorEnabled` en las personas.

### Otras notas del API

- En un 400 de validación, cada falla de `data` trae `attemptedValue`: el valor de un secreto (contraseñas, códigos, tokens, archivos y contraseñas de CSD/FIEL) llega enmascarado como `"[masked: n]"` (`n` es su longitud), o `"[masked]"` si la falla es de un objeto o una lista que lo contiene.
- En `taxCredentials` (facturas y cancelaciones por valores), `password` solo se exige en la llave privada (.key); en el certificado (.cer) el API no la usa. Al subir certificados con `getTaxFileService()->create()`, `password` sigue siendo obligatoria en el .cer y en la .key.

### Ejemplos

- `examples/examples.php`: sin `capitalRegime`, `userTypeId` `'C'` y los comentarios de `password` y `taxPassword` corregidos.
