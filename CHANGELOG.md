# Changelog

Cambios del SDK y notas de comportamiento del API de FiscalAPI que afectan a quien usa el SDK.

## [Sin publicar]

### Cambios incompatibles (BREAKING)

- El API deja de devolver `stripeCustomerId` y `subscriptionStatus` en las personas (`getPersonService()` y la persona de las reglas de descarga): eran datos internos de Stripe. El SDK no cambia, pero el arreglo de la persona ya no trae esas llaves: quite toda lectura de `$persona['stripeCustomerId']` o `$persona['subscriptionStatus']` (ahora da el aviso "Undefined array key" y `null`).

### Personas (`getPersonService()`)

El SDK envía y recibe las personas como arreglos, así que estos cambios del contrato no cambian código:

- El API no tiene `capitalRegime`: lo ignora y no lo devuelve. Envíe `legalName` sin régimen de capital.
- La respuesta incluye `phoneNumber`, `countryId`, `country`, `foreignTin`, `balances` (saldos por tipo de crédito: `creditType` y `available`; solo aparecen los tipos que la persona ha tenido) e `isOwner` (`true` si la persona es el owner de su tenant).
- `password`: requerida al crear; al actualizar, omitida, `null` o vacía conserva la contraseña actual. El API nunca la devuelve.
- `userTypeId`: `'C'` (cliente, el valor por omisión al crear) o `'U'` (usuario). `'T'` (tenant) solo llega en respuestas: el API lo rechaza al crear y al actualizar solo lo acepta si la persona ya es `'T'`.
- `taxPassword` es la contraseña de la llave privada (.key) que la persona guarda en su perfil; el API no la usa para sellar (al timbrar usa la contraseña de los certificados registrados o la de `taxCredentials`). Solo la reciben con valor la propia persona y el owner del tenant; los demás reciben `null`. Al actualizar, `null` la conserva y `''` la borra.
- El API ya no devuelve `twoFactorEnabled` en las personas.
- El API ya no devuelve `stripeCustomerId` ni `subscriptionStatus` en las personas (ver «Cambios incompatibles»).
- `validTo` y `committedBalance` son de solo lectura: el API los ignora al crear o actualizar. `validTo` es el fin de vigencia de la persona: lo asigna el API, casi siempre es `null` y es informativo (no limita el timbrado ni el acceso al API). `committedBalance` es un campo heredado que el API ya no calcula y siempre vale `0`; para el saldo use `availableBalance`, `availableValidationBalance` o `balances`.
- Personas: `create()` y `update()` con un correo que ya pertenece a otra persona, de esta o de otra cuenta (se compara sin distinguir mayúsculas ni los espacios de los extremos), responden 400 de validación con la falla en `Email` y el mensaje `The email is already registered.` (`getStatusCode()` 400 y la falla en `getJson()['data']`, con `propertyName` `Email`). Antes respondía otro 400 (una falla de Identity o el genérico `A record with the same unique values already exists.`). Al actualizar, el correo actual de la persona no cuenta como repetido. El API guarda el correo sin los espacios de los extremos.
- Personas: `update()` y `delete()` pueden responder 409 con `details` `The person was modified by another request (for example, a failed sign-in). Reload it and try again.` (`getStatusCode()` 409 y ese texto en `getJson()['details']`) si otra petición modificó a la persona mientras se procesaba (por ejemplo, un inicio de sesión fallido con su correo). No se guarda nada: vuelva a consultar la persona y repita la llamada. Antes la escritura se guardaba igual y podía deshacer el bloqueo de la cuenta por intentos fallidos.

### Otras notas del API

- En un 400 de validación, cada falla de `data` trae `attemptedValue`: el valor de un secreto (contraseñas, códigos, tokens, archivos y contraseñas de CSD/FIEL) llega enmascarado como `"[masked: n]"` (`n` es su longitud), o `"[masked]"` si la falla es de un objeto o una lista que lo contiene.
- En `taxCredentials` (facturas y cancelaciones por valores), `password` solo se exige en la llave privada (.key); en el certificado (.cer) el API no la usa. Al subir certificados con `getTaxFileService()->create()`, `password` sigue siendo obligatoria en el .cer y en la .key.
- `tin` es opcional en `getTaxFileService()->create()`: si se omite o va vacío, el API usa el RFC de la persona. Si se envía, debe ser el RFC de la persona (sin distinguir mayúsculas); si no, el API responde 400 con la falla en `Tin` (después de comprobar que puede gestionar los certificados de la persona; si no, 403). El API no guarda el valor enviado: el `tin` del archivo siempre es el RFC de la persona.
- `fileType` (al subir certificados y en `taxCredentials`) va como número: `0` = certificado (.cer), `1` = llave privada (.key). El API no acepta el nombre (`'CertificateCsd'`, `'PrivateKeyCsd'`): responde 400.
- Cambios del API en los endpoints de cuenta, que este SDK no usa (se autentica con API key), así que no le afectan: `POST /api/v4/account/register` y `POST /api/v4/account/info` con un correo que ya pertenece a otra persona responden 400 de validación con la falla en `Email` (`NewEmail` en `info`) y el mensaje `The email is already registered.`; `POST /api/v4/account/change-password` con la contraseña actual incorrecta responde 400 de validación con la falla `PasswordMismatch` (`Incorrect password.`); `POST /api/v4/account/refresh` con un refresh token vencido responde 401 `Invalid refresh token` en lugar de emitir otro (con uno vigente devuelve el mismo token), así que una sesión iniciada con `account/login` solo se puede renovar hasta 7 días después del login.

### Ejemplos

- `examples/examples.php`: sin `capitalRegime`, `userTypeId` `'C'` y los comentarios de `password` y `taxPassword` corregidos.
- `README.md`: los ejemplos "Subir Certificados CSD" y de factura por valores envían `fileType` como número (`0`/`1`) en lugar del nombre.
