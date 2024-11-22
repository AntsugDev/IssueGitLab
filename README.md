# Issue Project

## Languages

- PHP 8.3  
- Laravel 11  
- Node 20.11.0  
- NPM 10.2.4  
- Database: SQLite file located under the path *storage/app/database*  

## Login

The login is performed via SSO.

## Docker

<pre>docker build -t issue:stable .</pre>

<pre>docker run --name issue -p 8000:80 issue:stable </pre>

- App available at [localhost](http://localhost:8000)

---

## Basic Setup

- Create one or more *Milestones* (associated with the project, these are high-level groupings), similar to Epics on Jira. One is required for each project.  
- Create *labels*, which serve as tags to group various issues.  
- Build a dashboard to view issues based on the above groupings.  

## Methods to Create Labels

1. Copy and manually recreate the labels and dashboards from a base project.  
2. Use the APIs.  

To use the APIs, you need an *access token* on GitLab to read and write using the APIs.  
You can generate an access token from your profile by clicking on *Edit Profile* and then selecting the appropriate menu option on the right (see the screenshot below).  

<img src="public/img/createaccesstoken.png">

*Example API call*

Call the API to fetch the labels via GET (https://repo.bluarancio.com/api/v4/projects/:id_project/labels), passing your access token as a Bearer Token. Iterate over the response and call the insert API, as shown below:

<pre>
curl --location 'https://repo.bluarancio.com/api/v4/projects/:id/labels' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer ***********' \
--data '{
    "name": "Test",
    "description": "Test",
    "color": "#000000",
    "priority": 100
}'
</pre>

---

## Project Description

After logging in, register your access token and follow the steps below:

1. Choose the project.  
2. Replicate the *labels* in the project.  
3. Replicate the *dashboards*.  

Once logged in, you will be prompted to save your *access token*. If the operation is successful, the system will create the base data (for labels and boards) to be replicated in the selected project.  

Menu options include the following:

1. *Labels*: List of labels (no actions available).  
2. *Boards*: List of boards (no actions available).  
3. *Failed Jobs*: List of failed jobs.  
4. *Projects*: List of projects.  

Only the menu option under point *4* contains actionable items.  
The page appears as a table, displaying the following fields:  

- Project name  
- Project description  
- Whether labels are present  
- Whether boards are present  
- Actions:  
  - Repository link  
  - Project info  
  - Duplication of base labels/boards  

As implied by the last point, clicking the corresponding button will open a modal where you can choose what to replicate (all, boards, or labels).  

**NOTE:**  
- To replicate *boards*, the *labels* must be replicated first.  
- If you attempt to replicate *labels* in a project where they already exist, the API will return a 409 error, and the job will fail.  

Once a checkbox is selected and the button at the bottom left is clicked, the system will start the replication process as a background job. The result will not be visible immediately but may appear as a failed job if an error occurs.  

---

## Creating Issues

**Single/Manual Creation**  

Go to the dedicated section and create the issue with the following labels:  
- *Priority::normal*  
- *ToDo*  

**Import**  

The CSV file must contain the following:  

- HEADER:  
  **Title,Description,Labels**  

- BODY EXAMPLE:  
  `"Test","Test description","Label1,Label2"`

---

## Status Flow

- Initial status: *ToDo*  
- From the initial status, it can transition to *InProgress*.  
- From *InProgress*, it can transition to:  
  - *Wait*, if waiting for info or other details.  
  - *Closed*, if completed.  
- From *Closed*, it can transition to *ReOpen*.  
- From *ReOpen*, it can transition to *InProgress*.  

<img src="public/img/statusflow.png">

---

## Other Task Types

Tasks can have the following priorities:  

- Priority::low  
- Priority::normal  
- Priority::medium  
- Priority::high  

Additionally, there are tasks without priority:  

- BugFix  
- SubTask  
- Epic  

**NOTE:**  
An issue created with the *Epic* label acts as a parent issue for other issues. Essentially, it functions like a *Milestone*, with the difference that it is an issue.  
