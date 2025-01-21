<?php

class Tag

{
    private $tagId;
    private $tagName;
    private $db;

    public function __construct($tagId = null, $tagName = null)
    {
        $this->tagId = $tagId;
        $this->tagName = $tagName;
        $this->db = Database::getInstance()->getConnection();
    }

    // Getters
    public function getTagId()
    {
        return $this->tagId;
    }

    public function getTagName()
    {
        return $this->tagName;
    }

    // Setters
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;
    }

    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
    }


    public function deleteTag()
    {
        try {
            $sql = "DELETE FROM tags WHERE tag_id = :tag_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['tag_id' => $this->tagId]);
            return true;
        } catch (PDOException $e) {
            return "Error deleting tag: " . $e->getMessage();
        }
    }

    public function getAllTags()
    {
        try {
            $sql = "SELECT * FROM tags ORDER BY tag_name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return "Error fetching tags: " . $e->getMessage();
        }
    }

    public function addMultipleTags($tagString)
    {
        $tagNames = array_map('trim', explode(',', $tagString));
        $successCount = 0;
        $errorMessages = [];

        foreach ($tagNames as $tagName) {
            if (!empty($tagName)) {
                try {
                    $sql = "INSERT INTO tags (tag_name) VALUES (:tag_name)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(['tag_name' => $tagName]);
                    $successCount++;
                } catch (PDOException $e) {
                        $errorMessages[] = "Error adding tag '{$tagName}': " . $e->getMessage();
                    }
                }
            }

        if (empty($errorMessages)) {
            return "Successfully added {$successCount} tags.";
        } 
    }
    
}


