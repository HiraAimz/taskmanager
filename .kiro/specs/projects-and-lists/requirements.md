# Requirements Document

## Introduction

This document defines the requirements for the Projects and Lists functionality in the Laravel task management application. Projects provide a way to organize tasks within a workspace, while Lists provide a secondary level of organization within projects. This feature enables teams to structure their work hierarchically: Workspace → Projects → Lists → Tasks.

## Glossary

- **System**: The Laravel task management application
- **User**: An authenticated person using the System
- **Admin**: A User with the Admin role
- **Manager**: A User with the Manager role
- **Member**: A User with the Member role
- **Workspace**: A top-level organizational unit that contains Projects and Users
- **Workspace_Member**: A User who belongs to a Workspace
- **Project**: A container for related Tasks and Lists within a Workspace
- **Project_Owner**: The User designated as the owner of a Project
- **List**: A named collection of Tasks within a Project (stored as TaskList in the database)
- **Task**: A work item that belongs to a Project and optionally to a List
- **Archived_Project**: A Project with a non-null archived_at timestamp
- **Active_Project**: A Project with a null archived_at timestamp
- **Soft_Deleted_Project**: A Project with a non-null deleted_at timestamp

## Requirements

### Requirement 1: Project Creation

**User Story:** As a Manager or Admin, I want to create new projects within a workspace, so that I can organize tasks into distinct work areas.

#### Acceptance Criteria

1. WHEN an Admin requests to create a Project, THE System SHALL create the Project in the specified Workspace
2. WHEN a Manager who is a Workspace_Member requests to create a Project, THE System SHALL create the Project in the specified Workspace
3. WHEN a Member requests to create a Project, THE System SHALL deny the request
4. WHEN creating a Project, THE System SHALL require a name with at least 1 character and at most 255 characters
5. WHEN creating a Project, THE System SHALL set the creator as the Project_Owner
6. WHEN creating a Project, THE System SHALL allow optional description text
7. WHEN creating a Project, THE System SHALL allow a color value in hex format (7 characters including #)
8. WHEN creating a Project, THE System SHALL default the color to #3B82F6 if not specified
9. WHEN creating a Project, THE System SHALL allow an optional icon identifier (maximum 50 characters)
10. WHEN a Project is created, THE System SHALL log the creation activity with the User who performed the action

### Requirement 2: Project Viewing and Listing

**User Story:** As a User, I want to view projects in my workspaces, so that I can access and navigate to relevant work.

#### Acceptance Criteria

1. WHEN a User requests to view all Projects, THE System SHALL display only Active_Projects from Workspaces where the User is a Workspace_Member
2. WHEN displaying Projects, THE System SHALL eager-load the Project_Owner and Workspace relationships to prevent N+1 queries
3. WHEN displaying Projects, THE System SHALL include a count of Tasks in each Project
4. WHEN displaying Projects, THE System SHALL paginate the results with 12 Projects per page
5. WHEN displaying Projects, THE System SHALL order Projects by most recent first
6. WHEN a User requests to view a specific Project, THE System SHALL grant access if the User is a Workspace_Member of the Project's Workspace
7. WHEN an Admin requests to view any Project, THE System SHALL grant access

### Requirement 3: Project Updates

**User Story:** As a Manager or Admin, I want to update project details, so that I can maintain accurate project information.

#### Acceptance Criteria

1. WHEN an Admin requests to update a Project, THE System SHALL apply the changes
2. WHEN a Manager who is a Workspace_Member of the Project's Workspace requests to update a Project, THE System SHALL apply the changes
3. WHEN a Member requests to update a Project, THE System SHALL deny the request
4. WHEN updating a Project, THE System SHALL validate the name has at least 1 character and at most 255 characters
5. WHEN updating a Project, THE System SHALL validate the color is in hex format if provided
6. WHEN updating a Project, THE System SHALL validate the icon is at most 50 characters if provided
7. WHEN a Project is updated, THE System SHALL log the update activity with the User who performed the action
8. WHEN a Project is updated, THE System SHALL save changes to name, description, color, or icon fields

### Requirement 4: Project Archiving and Restoration

**User Story:** As a Manager or Admin, I want to archive and restore projects, so that I can hide completed projects without permanently deleting them.

#### Acceptance Criteria

1. WHEN an Admin requests to archive a Project, THE System SHALL set the archived_at timestamp to the current UTC time
2. WHEN a Manager who is a Workspace_Member requests to archive a Project, THE System SHALL set the archived_at timestamp to the current UTC time
3. WHEN a Member requests to archive a Project, THE System SHALL deny the request
4. WHEN a Project is archived, THE System SHALL exclude it from default Project listings
5. WHEN an Admin requests to restore an Archived_Project, THE System SHALL set the archived_at timestamp to null
6. WHEN a Manager who is a Workspace_Member requests to restore an Archived_Project, THE System SHALL set the archived_at timestamp to null
7. WHEN a Project is archived, THE System SHALL display a success message to the User
8. WHEN a Project is restored, THE System SHALL display a success message to the User

### Requirement 5: Project Deletion

**User Story:** As a Manager or Admin, I want to soft delete projects, so that I can remove projects that are no longer needed while maintaining data integrity.

#### Acceptance Criteria

1. WHEN an Admin requests to delete a Project, THE System SHALL soft delete the Project by setting the deleted_at timestamp
2. WHEN a Manager who is a Workspace_Member requests to delete a Project, THE System SHALL soft delete the Project by setting the deleted_at timestamp
3. WHEN a Member requests to delete a Project, THE System SHALL deny the request
4. WHEN a Project is deleted, THE System SHALL cascade the deletion to all associated Lists
5. WHEN a Project is deleted, THE System SHALL cascade the deletion to all associated Tasks
6. WHEN a Project is deleted, THE System SHALL cascade the deletion to all associated Statuses

### Requirement 6: List Creation

**User Story:** As a Workspace_Member, I want to create lists within a project, so that I can organize tasks into logical groupings.

#### Acceptance Criteria

1. WHEN a Workspace_Member requests to create a List in a Project they can view, THE System SHALL create the List
2. WHEN creating a List, THE System SHALL require a name with at least 1 character and at most 255 characters
3. WHEN creating a List, THE System SHALL assign it to the specified Project
4. WHEN creating a List, THE System SHALL calculate the position as the highest existing position plus 1
5. WHEN creating a List in a Project with no existing Lists, THE System SHALL set the position to 0
6. WHEN a List is created, THE System SHALL log the creation activity with the User who performed the action
7. WHEN a User who is not a Workspace_Member of the Project's Workspace requests to create a List, THE System SHALL deny the request

### Requirement 7: List Viewing

**User Story:** As a User, I want to view lists within a project, so that I can see how tasks are organized.

#### Acceptance Criteria

1. WHEN a User requests to view Lists for a Project, THE System SHALL display all Lists for that Project ordered by position ascending
2. WHEN displaying Lists, THE System SHALL show the List name
3. WHEN displaying Lists, THE System SHALL include a count of Tasks in each List
4. WHEN displaying an empty Project, THE System SHALL show a message indicating no Lists exist
5. WHEN a User requests to view Lists for a Project they cannot access, THE System SHALL deny the request

### Requirement 8: List Updates

**User Story:** As a Workspace_Member, I want to rename lists, so that I can keep list names accurate and meaningful.

#### Acceptance Criteria

1. WHEN a Workspace_Member requests to update a List in a Project they can view, THE System SHALL apply the name change
2. WHEN updating a List, THE System SHALL require a name with at least 1 character and at most 255 characters
3. WHEN a List is updated, THE System SHALL log the update activity with the User who performed the action
4. WHEN a User who is not a Workspace_Member requests to update a List, THE System SHALL deny the request

### Requirement 9: List Reordering

**User Story:** As a Workspace_Member, I want to reorder lists within a project, so that I can arrange them in a logical sequence.

#### Acceptance Criteria

1. WHEN a Workspace_Member requests to reorder Lists, THE System SHALL update the position values for all affected Lists
2. WHEN reordering Lists, THE System SHALL maintain sequential integer positions starting from 0
3. WHEN reordering Lists, THE System SHALL ensure no duplicate position values exist within a Project
4. WHEN Lists are reordered, THE System SHALL apply changes in a single database transaction
5. WHEN a User who is not a Workspace_Member requests to reorder Lists, THE System SHALL deny the request

### Requirement 10: List Deletion

**User Story:** As a Manager or Admin, I want to delete lists, so that I can remove organizational structures that are no longer needed.

#### Acceptance Criteria

1. WHEN an Admin requests to delete a List, THE System SHALL permanently delete the List
2. WHEN a Manager who is a Workspace_Member requests to delete a List, THE System SHALL permanently delete the List
3. WHEN a Member requests to delete a List, THE System SHALL deny the request
4. WHEN a List is deleted, THE System SHALL cascade the deletion to all Tasks in that List
5. WHEN a List is deleted, THE System SHALL log the deletion activity with the User who performed the action
6. WHEN a List containing Tasks is deleted, THE System SHALL display a confirmation prompt before deletion

### Requirement 11: Default List Behavior

**User Story:** As a User, I want tasks to be assigned to a default list if I don't specify one, so that task creation is not blocked by missing list assignments.

#### Acceptance Criteria

1. WHEN a Task is created without specifying a List, THE System SHALL assign the Task to a List named "Default" in the Project
2. WHEN assigning a Task to the Default List and no Default List exists, THE System SHALL create a Default List with position 0
3. WHEN creating the Default List, THE System SHALL set the name to "Default"
4. THE System SHALL allow Users to rename the Default List
5. THE System SHALL allow Users to delete the Default List if it contains no Tasks

### Requirement 12: Project-Workspace Relationship

**User Story:** As a User, I want projects to belong to a single workspace, so that organizational boundaries are clearly defined.

#### Acceptance Criteria

1. THE System SHALL require every Project to belong to exactly one Workspace
2. WHEN a Workspace is deleted, THE System SHALL cascade the deletion to all associated Projects
3. WHEN displaying a Project, THE System SHALL display the Workspace name
4. THE System SHALL prevent reassigning a Project to a different Workspace

### Requirement 13: List-Project Relationship

**User Story:** As a User, I want lists to belong to a single project, so that task organization is clear and unambiguous.

#### Acceptance Criteria

1. THE System SHALL require every List to belong to exactly one Project
2. WHEN a Project is deleted, THE System SHALL cascade the deletion to all associated Lists
3. WHEN displaying a List, THE System SHALL display the Project name
4. THE System SHALL prevent reassigning a List to a different Project

### Requirement 14: Database Indexing for Performance

**User Story:** As a System Administrator, I want appropriate database indexes on projects and lists, so that queries perform efficiently as data grows.

#### Acceptance Criteria

1. THE System SHALL maintain an index on the project name column
2. THE System SHALL maintain a composite index on workspace_id and archived_at columns in the projects table
3. THE System SHALL maintain an index on the owner_id column in the projects table
4. THE System SHALL maintain a composite index on project_id and position columns in the task_lists table
5. WHEN querying Projects by Workspace and archive status, THE System SHALL use the composite index

### Requirement 15: Activity Logging

**User Story:** As an Admin, I want all project and list changes logged, so that I can audit who made changes and when.

#### Acceptance Criteria

1. WHEN a Project is created, THE System SHALL log the activity with all fillable attributes
2. WHEN a Project is updated, THE System SHALL log the activity with changed attributes and the User who made the change
3. WHEN a Project is deleted, THE System SHALL log the activity with the User who performed the deletion
4. WHEN a List is created, THE System SHALL log the activity with all fillable attributes
5. WHEN a List is updated, THE System SHALL log the activity with changed attributes and the User who made the change
6. WHEN a List is deleted, THE System SHALL log the activity with the User who performed the deletion

### Requirement 16: Project Task Count Display

**User Story:** As a User, I want to see the number of tasks in each project, so that I can quickly assess project workload.

#### Acceptance Criteria

1. WHEN displaying a list of Projects, THE System SHALL show the total count of Tasks for each Project
2. WHEN calculating Task count, THE System SHALL include Tasks from all Lists in the Project
3. WHEN calculating Task count, THE System SHALL exclude soft-deleted Tasks
4. THE System SHALL compute the Task count using a database aggregate query to avoid loading all Task records

### Requirement 17: List Task Count Display

**User Story:** As a User, I want to see the number of tasks in each list, so that I can understand how work is distributed.

#### Acceptance Criteria

1. WHEN displaying Lists within a Project, THE System SHALL show the count of Tasks for each List
2. WHEN calculating Task count for a List, THE System SHALL exclude soft-deleted Tasks
3. THE System SHALL compute the Task count using a database aggregate query to avoid loading all Task records

### Requirement 18: Project Color and Icon Display

**User Story:** As a User, I want projects to have customizable colors and icons, so that I can visually distinguish between different projects.

#### Acceptance Criteria

1. WHEN displaying a Project, THE System SHALL show the Project color as a visual indicator
2. WHEN displaying a Project, THE System SHALL show the Project icon if one is set
3. WHEN creating or updating a Project, THE System SHALL validate the color is a 7-character hex code including the # symbol
4. WHEN creating or updating a Project, THE System SHALL accept standard CSS color hex values
5. WHEN a Project is created without specifying a color, THE System SHALL default to #3B82F6 (blue)

### Requirement 19: Permission Validation Consistency

**User Story:** As a System Administrator, I want consistent permission checks across all project and list operations, so that security is enforced uniformly.

#### Acceptance Criteria

1. THE System SHALL use Laravel Policy classes to enforce all Project permissions
2. THE System SHALL use Laravel Policy classes to enforce all List permissions
3. WHEN a User attempts an unauthorized action, THE System SHALL return a 403 Forbidden response
4. THE System SHALL check permissions before executing any create, update, or delete operation
5. THE System SHALL check permissions in both HTTP controllers and Livewire components

### Requirement 20: Workspace Membership Verification

**User Story:** As a System Administrator, I want project access to be restricted by workspace membership, so that users can only access projects in their workspaces.

#### Acceptance Criteria

1. WHEN checking if a User can view a Project, THE System SHALL verify the User is a Workspace_Member of the Project's Workspace
2. WHEN checking if a Manager can modify a Project, THE System SHALL verify the Manager is a Workspace_Member of the Project's Workspace
3. THE System SHALL exempt Admins from Workspace membership checks
4. WHEN verifying Workspace membership, THE System SHALL query the workspace_user pivot table
5. THE System SHALL cache Workspace membership checks within a single request to avoid redundant queries
