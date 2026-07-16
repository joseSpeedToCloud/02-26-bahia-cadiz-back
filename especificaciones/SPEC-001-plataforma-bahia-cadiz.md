# SPEC-001 — Plataforma Digital de la Bahía de Cádiz

**Expediente:** 312/2026 — Mancomunidad de Municipios de la Bahía de Cádiz
**Estado:** En desarrollo

## Objeto

Plataforma SaaS de gestión de contenidos con app móvil, orientada a centralizar información turística y de movilidad de los municipios miembros de la Mancomunidad. Alineada con la norma UNE 178503 (semántica turística).

## Módulos

- **CMS headless (Drupal)** — backend unificado de contenidos, repositorio principal integrable con el resto de módulos.
- **Portal web** responsive + **app móvil** (PWA, Angular/Ionic recomendado).
- **Agenda de Eventos** — cultura, ocio y actividades de los municipios.
- **Playas** — estado del mar, ocupación, banderas, accesibilidad en tiempo real.
- **Movilidad Sostenible** — transporte público, aparcamientos, calculadora de huella de carbono.

## Alcance de este panel de tasks

Este panel cubre únicamente la **parte de backend**: infraestructura cloud/Terraform y el núcleo del CMS headless. Los módulos de frontend (Portal, app móvil, Agenda, Playas, Movilidad UI) y el resto de categorías del pliego (Diseño/UX, Integración, Contractual/Administrativo, Gestión, Gestión Personal) se planificarán en paneles/specs separados cuando toque.

## Restricciones y condiciones contractuales relevantes para el backend

- **Condición especial B (penalización 3%):** el datacenter/región de GCP elegido debe tener certificación de eficiencia energética (ISO 50001 o equivalente) y energía renovable — ver TASK-007.
- **Condición especial A (penalización 3%):** toda la documentación/informes generados por el equipo deben entregarse en formato digital.
- SLA de disponibilidad y tiempos de intervención comprometidos en la oferta tienen **carácter contractual** — no solo de propuesta.
- No se permite subcontratar el desarrollo principal.
- Todo el código y documentación son propiedad de la Mancomunidad; entrega en repositorio Git obligatoria para la recepción definitiva.
- Librerías de terceros: solo GPL/MIT/Apache o licencias comerciales perpetuas transferibles.

## Stack técnico

- **CMS:** Drupal (headless / decoupled), JSON:API o GraphQL para exposición de contenidos.
- **Infraestructura:** GCP, gestionada con Terraform, despliegue en Cloud Run o GKE.
- **CI/CD:** GitHub Actions.
- **Cumplimiento:** ENS categoría básica, RGPD/LOPDGDD.

## Referencias

- PPT-BahiaCadiz.pdf, PCAP-BahiaCadiz.pdf, Normativa_y_Guion_Desarrollo_BahiaCadiz.pdf
- `Checklist_PlataformaDigitalBahiaCadiz.xlsx` (141 requisitos, pestaña "Checklist Completo")
- Ver `panel/README.md` para el estado de las tasks derivadas de esta spec.
