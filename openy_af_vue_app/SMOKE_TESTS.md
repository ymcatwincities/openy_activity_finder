# Welcome to Activity Finder 3 smoke tests documentation

In order for Open Y Activity Finder 3 being tested in a short timeframe, please follow steps below

# Paragraph: Activity Finder 3 (Solr back-end)							

## Check DEMO page with Activity Finder

### User

Administrator

### Steps

1. Login as Admin
2. Go to Content page
3. Search by title ""Activity Finder""
4. Verify there is a landing page with such a title
5. Open it  
6. Verify this page contains the following elements:
   - The textfield with title Program Search and placeholder ""Keywords...""
   - The button with search icon
   - Under the search field should be link the ""View all programs"" that should display all results
   - Under the View all programs should be displayed the number of results
   - The block with title ""Activity Finder"" filters 
   - Three filters which look like buttons per row: By Age, By Day of week, By Activity
 7. Verify the page is functional and all elements are working well

### Expected Results

1. There is a demo landing page with functional Activity Finder on it
2. You can interact with all elements of this block

## Adding paragraph with Activity Finder to a page

### User

Administrator

### Steps

1. Log as Admin
2. Go to the Add content -> Create Landing Page (/node/add/landing_page)
3. In the Content area verify you can add a paragraph called ""Add Activity Finder""
4. Verify ""Activity Finder"" paragraph has the following fields:
  - Limit by category (reference field)
  - Activity Finder Results Page Reference (reference field)
  - Categories exclude (multivalue) (reference field)
  - Hide Locations selection step (checkbox)
  - Hide Program Categories (checkbox)
  - Show filter groups opened (checkbox)
  - Hide program and home branch block (checkbox)
  - Block has value Activity Finder Block (should be required)
  - Title (should be required)
  - Display title  (checkbox)
5. Verfiy there are no errors and page created successfully using paragraph
6. Verify you can edit the page, edit paragraph, without any issues

### Expected Results

1. There is a paragraph called ""Activity Finders"" that can be added to a landing page
2. Added paragraphs works well after saving the page 

## Check Activity Finder paragraph on the page

### User

Anonymous

### Steps

1. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
2. Verify this page contains the following elements:
   - The textfield with title Program Search and placeholder ""Keywords...""
   - The button with search icon
   - Under the search field should be link the ""View all programs"" that should display all results
   - Under the View all programs should be displayed the number of results
   - The block with title ""Activity Finder"" filters 
   - Three filters which look like buttons per row: By Age, By Day of week, By Activity
3. Verify that you can interact with form elements: search bar, buttons, Links
4. Enter keyword, for example ""swim"" and verify you redirected to the results page with results on it

### Expected Results

1. On the activity finder landing page you can see the block called ""Activity Finders"" 
2. You can interact with all elements of this block
3. While choosing diffeent filter or search by keyword results are correct.

## Check "View all programs" link

### User

Anonymous

### Steps

1. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
2. Click on the ""View all programs"" link
3. Verify you are redirected to the results page
4. Verify you see all programs and the number of resuls is the same as on the previous step


### Expected Results

1. ""View all programs"" link redirect user to the results page with all programs 
2. Number of results is the same as on the landing page 
3. You should see in sidebar the following filters that grouped and in each group you should be able to see filters itself and can be able to select them
 - Schedule preferences
 - Programs & Classes
 - Location preferences

## Check filter functionality

### User

Anonymous

### Steps

1. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 

2. Search by keyword:
2.1 In the search field insert a keyword for searching activities with this keywords (for. ex ""Family Activities"")
2.2 Click search button (near the search string with search icon)
2.3 Verify you are redirected to the results page

3. Filter by age:
3.1 Click on the ""By age"" blue button
3.2 On th next step click on the Age(s) section to expand it, then select needed age/ages
3.3 On the next step select the Day/days of the week or skip it (by clicking ""Skip"" link)
3.4 On the next step you can select or skip types of activities
3.5 On the next step you can select or skip location
3.6 You should be redirected to result page with filtered activities

4. Filter by day of week:
4.1 click on the ""By day of week"" button
4.2 On the next step click on the Day of the week section for expand it, then select needed day/days
4.3 On the next step check the Age(s) or skip it (by clicking ""Skip"" link)
4.4 On the next step you can select or skip types of activities
4.5 On the next step you can select or skip location
4.6 You should be redirected to result page with filtered activities

5. Filter by Activity
5.1 Click on the ""By Activity"" button
5.2 On the next step you can expand group of activities by cicking on it, then select needed activitiy/activities (you can select a few activities)
5.3 On the next step check the Age(s) or skip it (by clicking ""Skip"" link)
5.4 On the next step you can select or skip days of week
5.6 On the next step you can select or skip location
5.6 You should be redirected to result page with filtered activities


### Expected Results

1. You should be redirected to results page.

2. Search by keyword:
2.1 In the left sidebar in the search field you should see keyword from previous step
2.2. You should see activities that related to entered keyword
2.3. You should have ability to get more information about activity (by click on activity card)
2.4. In the modal windows after click by activity card you should have ability to register to this activity
2.5. You should have ability to filter results byfollowing filters 
 - Schedule preferences
 - Programs & Classes
  Location preferences

3. Filter by age:
3.1 You can filter activities by Age/ages first
3.2 On the results page found programs are correct, according to selected filter

4. Filter by week:
4.1 You can filter activities by Day of week
4.2 On the results page found programs are correct, according to selected filter

5. Filter by activity:
5.1 You can filter activities by activities
5.2 On the results page found programs are correct, according to selected filter

## Check "Start Over"

### User

Anonymous

### Steps

1. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
2. Search by keyword or using one of the available filters 
3. On any page (except the initial page) you should have a link ""Start over"" in the page header
4. Click by this link, then you should be redirected on start screen with default states of filters


### Expected Results

1. On all pages except the initial page should be displayed a link ""Start Over""
2. After cicking on the ""Start Over"" link search should start from scratch

## Check filters functionality

### User

Anonymous

### Steps

1. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
2. Click by the ""View all programs"" link
3. You should be redirected to the results page
4. Play with filters by selecting different values
5. Verify results are updating without reloading the page
6. Verify soring by different options is working


### Expected Results

1. In left sidebar you should see filters:
- Searchbar by searching by kyewords
- Expandable filters in different groups (see test case below)
2. By clicking on any filters the results should be updated with counting of selected filter
3. On the top of results you should be able to sort results: 
- Sort by Title (A-Z)
- Sort by Title (Z-A)
- Sort by Location (A-Z)
- Sort by Location (Z-A)
- Sort by Activity (A-Z)
- Sort by Activity (Z-A)
4. All manipulations on the page happens without reloading the page

## Check paragraph settings

### User

Administartor

### Steps

 Limit by category
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. In the ""Limit by category"" field select category (for. Group Exercise Classes)
5. Save node and check the Activity Finder


### Expected Results

1. When you visit the Activity Finder Landing page you should be automatically redirected on the first step Ages
2. All activities should be filtered by selected in the ""Limit by category"" field value
3. The step of selecting a type of Activity should be skipped

## Check paragraph settings

### User

Administartor

### Steps

Categories exclude
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. In the Categories exclude select one or a few categories (for. ex. Group Exercise Classes)
5. Save node and check the Activity finder


### Expected Results

1.In the Activity step, you shouldn't see categories that you excluded

## Check paragraph settings

### User

Administartor

### Steps

Hide Locations selection step
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. Check the ""Hide Locations selection step"" checkbox
5. Save node and check the Activity finder


### Expected Results

1. The select location step sould be skipped

## Check paragraph settings

### User

Administartor

### Steps

Hide Program Categories
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. Check the ""Hide Program Categories"" checkbox
5. Save node and check the Activity finder


### Expected Results

1. The select location step sould be skipped

## Check paragraph settings

### User

Administartor

### Steps

Show filter groups opened
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. Check the ""Show filter groups opened"" checkbox
5. Save node and check the Activity finder


### Expected Results

1. All group of filters on each steps should be expanded and can't be collapsed

## Check paragraph settings

### User

Administartor

### Steps

Hide program and home branch block
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. Check the ""Hide program and home branch block"" checkbox
5. Save node and check the Activity finder


### Expected Results

1. On start step of activity finder should be hidden the search box for searching by keywords, the links to all results  and count of results

## Check paragraph settings

### User

Administartor

### Steps

Display title
1. Log in as admin
2. Go to existing ""Activity Finder"" page (see test case above) or create a new landing page with paragraphs added (see test case above) 
3. Click edit on Activity Finder paragraph
4. Check the ""Display title"" checkbox
5. Save node and check the Activity finder


### Expected Results

1. Check title is displayed
