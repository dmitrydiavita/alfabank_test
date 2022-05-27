<?php

namespace App\Tests;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryApiControllerTest extends WebTestCase
{
    public function testEmptyResult(): void
    {
        $client = static::createClient();
        $client->request('GET', '/category-list');
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals([], $data, "Result is not empty");
    }

    public function testCreateEntity(): void
    {
        $client = static::createClient();
        $name = 'Apple';

        $client->request('POST', '/category-create', [
            'name' => $name,
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, empty($data['error']), $data['error'] ?? '');
        $this->assertEquals(true, $data['success'], "Create category failed");

        /** @var Category $category */
        $category = $this->getContainer()->get('doctrine')->getRepository(Category::class)->find($data['id']);
        $this->assertEquals($name, $category->getName(), "Wrong name");
    }

    public function testCreateEntityError(): void
    {
        $client = static::createClient();

        $client->request('POST', '/category-create', [
            'name' => null,
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(false, empty($data['error']), 'Must be error');
    }

    public function testReadEntity(): void
    {
        $client = static::createClient();

        $name = 'Banana';
        $category = new Category();
        $category->setName($name);
        $doctrine = $this->getContainer()->get('doctrine.orm.entity_manager');
        $doctrine->persist($category);
        $doctrine->flush();

        $client->request('GET', "/category-read/{$category->getId()}");
        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, isset($data['name']), "Name is not presented in result");
        $this->assertEquals($name, $data['name'], "Wrong name in result");
    }

    public function testEditEntity(): void
    {
        $client = static::createClient();

        $name1 = 'TV1';
        $name2 = 'TV2';
        $category = new Category();
        $category->setName($name1);
        $doctrine = $this->getContainer()->get('doctrine.orm.entity_manager');
        $doctrine->persist($category);
        $doctrine->flush();

        $client->request('POST', "/category-edit/{$category->getId()}", [
            'name' => $name2,
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['success'] ?? false, "Update category failed");

        /** @var Category $category */
        $category = $doctrine->getRepository(Category::class)->find($category->getId());
        $this->assertEquals($name2, $category->getName(), "Wrong name");
    }

    public function testDeleteEntity(): void
    {
        $client = static::createClient();

        $name = 'Chair';
        $category = new Category();
        $category->setName($name);
        $doctrine = $this->getContainer()->get('doctrine.orm.entity_manager');
        $doctrine->persist($category);
        $doctrine->flush();

        $categoryId = $category->getId();
        $client->request('POST', "/category-delete/{$categoryId}");

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(true, $data['success'] ?? false, "Delete category failed");

        /** @var Category $category */
        $category = $doctrine->getRepository(Category::class)->find($categoryId);
        $this->assertEquals(null, $category, "Category not deleted");
    }
}
