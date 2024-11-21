# Issue project

## Linguaggi 

- php 8.3
- laravel 11
- node 20.11.0
- npm 10.2.4
- database sqlite in file presente sotto il path *storage/app/database*

## Login

La login avviene attraverso SSO realm operatore.

## Docker

<pre>docker build -t issue:stable .</pre>

oppure

<pre>docker pull antoniosugamelesps/gitlab_api:stable</pre>

<pre>docker run --name issue -p 8000:80 issue:stable </pre>

- app all'url [localhost](http://localhost:8000)

***
***

## Impostazioni di base


- Creare un o più *Milestones*, che rapperesentano quelli che su Jira sono gli Epic (un raggruppamento); questi per ogni progetto.
- Vanno creati i *labels*, che rappresentano un modo di raggruppare in tag i vari issue
- Creare una dashboard per la visualizzazione degli issue, secondo i raggruppamenti sopra

## Modi di creare i labels

1. copiare e ricreare a mano da un progetto base i vari labels e le dashboard
2. Usare le api

Per usare le api si necessità di un *access token*, su gitLab per leggere e scrivere utilizzando le api
L'accessToken si può creare dal proprio profilo basta cliccare su *Edit* e poi sulla voce di Menù a destra.

Chiamo l'api in get dei labels (https://repo.bluarancio.com/api/v4/projects/:id_project/labels), dove si passa come Bearer Token il vostro access token,
ciclarla e chiamare l'api di insert che è questa:

<pre>
curl --location 'https://repo.bluarancio.com/api/v4/projects/:id/labels' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ***********' \
--data '{
    "name": "Test",
    "description": "Test",
    "color": "#000000",
    "priority":100
}'
</pre>

Per evitare ciò sto creando un progetto (che useremo solo noi), si veda il punto sotto [Passaggi](Passaggi).

## Passaggi
Si entra con l'utenza di operatore personale (questo così in fase di creazione si vede chi ha creato cosa), si registra il proprio access token e si seguono i passaggi sotto. 

1. scegliere il progetto
2. replicare nel progetto i labels
3. replicare le *dashboard* :
   - *Custom*
   - *Type Task*


## Creazione issue

**Singolo/Manuale**

Si và nella sezione dedicata e deve essere creato con i labels:
- *Task::normal**
- *ToDo*

**Import**

Il file csv deve contenere quanto segue:
- HEADER:
**Title,Description,Labels**

- BODY EXAMPLE
"Test","Test description","Etichetta labels separate da ,"


## Status flow

- Stato iniziale: *ToDo*
- Dallo stato inziale si può passare in *InProgress*
- Dallo stato *inProgress*, si può passare agli stati:
  - *Wait*, se si è in attesa di info o altro
  - *Closed*, se è terminato
- Dallo stato *Closed*, si può passare allo stato *ReOpen*
- Dallo stato *ReOpen* si può passare allo stato *InProgress*


## Altre tipologie di task

Sono previsti dei task con priorità:

- Task-high
- Task-medium
- Task-normal
- Task-low

Inoltre, esistono i task senza priorità:
- BugFix
- SottoTask
