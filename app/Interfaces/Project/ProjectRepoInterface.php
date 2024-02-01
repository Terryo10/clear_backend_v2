<?php

namespace App\Interfaces\Project;

interface ProjectRepoInterface
{
    public function getProject($id);
    public function getProjects();
    public function createProject(array $data);
    public function updateProject($data, $id);
    public function deleteProject($id);
    public function filter($title, $status, $user, $from, $to, $service, $contractor);
    public function createProjectHistory($project, $status, $description);
    public function setPaymentLink(array $data);
    public function changePaymentStatus(array $data);
}
