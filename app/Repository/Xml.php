<?php
namespace App\Repository;

class Xml extends Main
{

    public $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function run($file, $arMergeFiles)
    {
        if (empty($file) && empty($arMergeFiles)) return;
        $arPosts = [];
        if (!$file && !empty($arMergeFiles)) {
            $file = 'merge';
            foreach ($arMergeFiles as $arMergeFile) {
                $arTempPost = $this->getPosts($arMergeFile);
                $arPosts = array_merge($arPosts, $arTempPost);
            }
        } else {
            $arPosts = $this->getPosts($file);
        }

        $rssfeed = '<?xml version="1.0" encoding="UTF-8" ?>
            <rss version="2.0"
             xmlns:excerpt="http://wordpress.org/export/1.1/excerpt/"
             xmlns:content="http://purl.org/rss/1.0/modules/content/"
             xmlns:wfw="http://wellformedweb.org/CommentAPI/"
             xmlns:dc="http://purl.org/dc/elements/1.1/"
             xmlns:wp="http://wordpress.org/export/1.1/"
            >
            
            <channel>
             <wp:wxr_version>1.1</wp:wxr_version>';
        foreach ($arPosts as $arPost) {
            $rssfeed .= '
                <item>
                <title>' . $arPost["post_title"] . '</title>
                <dc:creator>admin</dc:creator>
                <description></description>
                <content:encoded><![CDATA[' . $arPost["post_content"] . ']]></content:encoded>
                <wp:post_id></wp:post_id>
                <wp:comment_status>open</wp:comment_status>
                <wp:ping_status>open</wp:ping_status>
                <wp:status>publish</wp:status>
                <wp:post_type>post</wp:post_type>
                <category domain="category" nicename="name"><![CDATA[Название категории]]></category>
                </item>';
        }

        $rssfeed .= '</channel>';
        $rssfeed .= '</rss>';

        $fileXml = $this->app->io->getXmlFolder() . "/" . $file . ".xml";
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($rssfeed);
        $dom->formatOutput = true;
        $dom->save($fileXml);

        return "/upload/xml/" . $file . ".xml";
    }


    public function getUsers($file)
    {
        $arResult = [];
        $res = $this->app->db->query("SELECT * FROM `users` WHERE `file_name` LIKE '$file'");
        while ($user = $res->fetch(\PDO::FETCH_ASSOC)) {
            $user['file_name'] = $file;
            $arResult[] = $user;
        }
        return $arResult;
    }

    public function getOptions($file)
    {
        $arResult = [];
        $res = $this->app->db->query("SELECT * FROM options  WHERE `file_name` LIKE '$file'");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $arResult[$row['option_name']] = $row['option_value'];
        }
        return $arResult;
    }

    public function getCategory($file)
    {
        $arResult = [];
        $res = $this->app->db->query("SELECT * FROM category WHERE `file_name` LIKE '$file'");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $arResult[] = $row;
        }
        return $arResult;
    }

    public function getPosts($file)
    {
        $arPosts = [];
        $res = $this->app->db->query("SELECT * FROM `posts` WHERE `file_name` LIKE '$file'");
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $arPosts[] = $row;
        }
        return $arPosts;
    }

}