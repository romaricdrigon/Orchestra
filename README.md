Orchestra Bundle
================

Orchestra is a Naked Object implementation on top of Symfony2
Available as a Symfony2 Bundle

[![Latest Stable Version](https://poser.pugx.org/romaricdrigon/orchestra-bundle/v/stable.svg)](https://packagist.org/packages/romaricdrigon/orchestra-bundle)
[![License](https://poser.pugx.org/romaricdrigon/orchestra-bundle/license.svg)](https://packagist.org/packages/romaricdrigon/orchestra-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/69a47b43-e701-439f-bc13-d46558286f54/mini.png)](https://insight.sensiolabs.com/projects/69a47b43-e701-439f-bc13-d46558286f54)
[![Build Status](https://travis-ci.org/romaricdrigon/OrchestraBundle.svg?branch=master)](https://travis-ci.org/romaricdrigon/OrchestraBundle)

## Why Orchestra?

[Naked Object](http://en.wikipedia.org/wiki/Naked_objects) is a powerful pattern first described by Richard Pawson.  
It states that business objects, if correctly modelled, are sufficient to create an application, the technical code being generic enough to be automatically generated.

Orchestra aims to be such an admin generator: a library that helps you focusing on modelling the Domain right, and do all the "boring code" for you.  

Maybe you will want to personalize it further, then it's ok, Orchestra is not about restraining you but helping you quickly create a starter application, that may be used as a prototype, a proof-of-concept, or for discussions with business experts...
Note that Orchestra application are made to be production-ready, safe to use, and to offer decent performances. Everything is done by reflection (eventually cached), no direct code generation in here.

## Roadmap / version

Roadmap:

 * **0.8** (current): functional but still **beta** version. Using is OK, but extending may change
 * 0.9 : production-ready. Several code optimizations are needed
 * 1.0 : first stable version. Some refactors have to occur before, but not after before a 2.0 version.

## Installation

Install bundle using composer: `composer require romaricdrigon/orchestra-bundle`

Register the bundle and the vendor we use in `app/AppKernel.php`:
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new RomaricDrigon\OrchestraBundle\RomaricDrigonOrchestraBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        );
```

Import our routes (both the XML and our custom type):
```yaml
# app/config/routing.yml
orchestra_routing:
    resource: '@RomaricDrigonOrchestraBundle/Resources/config/routing.xml'
    prefix: /admin

orchestra_generated:
    prefix: /admin
    resource: .
    type: orchestra
```

## Getting started

With Orchestra admin generator you will have to focus only on 2 objects: `Entities` and `Repositories`.
All those objects must be placed within a valid Symfony2 bundle.

**TL;DR:** you can find a working example at [Orchestra-example](https://github.com/romaricdrigon/Orchestra-example)

### Entities

`Entities` are the basic Domain objects of your application.
Even if we will see later that they are persisted, they may be differ than Doctrine entities.

#### Basic entity

By convention, place those in your bundle `Entity` folder.

They must implement `RomaricDrigon\OrchestraBundle\Domain\Entity\EntityInterface`: Orchestra must be able to get a unique ID for each entity instance.
```php
use RomaricDrigon\OrchestraBundle\Domain\Entity\EntityInterface;

class SomeEntity implements EntityInterface
{
    public function getId()
    {
        return ...
```

Naked Object follow a DDD mindset.
We strike not to have an [Anemic Domain Model](http://www.martinfowler.com/bliki/AnemicDomainModel.html).
A few guidelines and advices:

 * entities properties are private or protected, *not public*,, in compliance with encapsulation
 * entities must have public getters for the properties you will want to expose, for example in the views
 * they expose methods corresponding to actions on the objects, leading to modification of its internal state, but **not public setters**
 * you may want to add *private* setters, in order to achieve self-encapsulation, it's up to you

#### Displaying the entity in listing

If you want your entity to be displayed in the default listing page, it must implement `ListableEntityInterface`:

```php
use RomaricDrigon\OrchestraBundle\Domain\Entity\EntityInterface;
use RomaricDrigon\OrchestraBundle\Domain\Entity\ListableEntityInterface;

class SomeEntity implements EntityInterface, ListableEntityInterface
{
    public function viewListing()
    {
        return ['some data', 'some more', '...'];
    }
```

The data returned by `viewListing` will be displayed in the same order in each listing row.
It can be an array or a `QueryInterface` object. You will learn more about queries in the next sections.

#### Command and queries

Entities apply the [Command-Query Separation principle](http://martinfowler.com/bliki/CommandQuerySeparation.html).

##### Query

Any entity method can return an array (simpler, preferred) or an object implementing `QueryInterface`.
Orchestra will generate from it an action, a web page displaying the data from the returned object.

*Note*: `QueryInterface` extends `\Traversable`.
It means that a Query will have to either extend [`\Iterator`](http://php.net/manual/en/class.iterator.php) or [`IteratorAggregate`](http://php.net/manual/en/class.iteratoraggregate.php)
Watch out, as of PHP 5.4.30, interface implementation order counts, you must implement one those 2 interfaces before implementing `QueryInterface`.

##### Command

Any entity method can accept an object implementing `CommandInterface`.
Such method will be transformed into a web page with a Form.

A `Command` is typically a simple data container, with public properties. Those public properties will be mapped to Orchestra-generated Form.

*Note*: A Command will be called either by calling its constructor (in that case it should receive no argument), either you can designate a factory method on the entity.
In that case, use the `CommandFactory` annotation on your Command class.

#### Persisting

Usually, they are persisted. Orchestra supports Doctrine ORM through its Symfony2 bridge.
You will have to do their mapping, using Doctrine annotations, YAML (advised) or XML as you want.
For this part, please refer to [Symfony documentation](http://symfony.com/doc/2.4/book/doctrine.html).

#### Events

Events are objects implementing `EventInterface`. An Entity can emit Events.

A method emitting en Event must return either an Event or `null` (in that case nothing will happen).
The `Event` will be passed to the corresponding Repository `receive` method (*it must implement `ReceiveEventInterface`*). You can then decide what to do.

### Repositories

Those are NOT Doctrine repositories!
We stick to the original Repository pattern (Fowler, 2002), "a collection-like interface for accessing domain objects".

Their name will be the name of the `Entity` suffixed by `Repository` but you're free to do otherwise.

**Each `Repository` must have a corresponding `Entity`, with the same slug (lowercase name without suffix):
 for a `FooBar` entity, you must have a `FoobarRepository`, or `FooBarRepository` or `FooBar` (though this last is less readable).**

#### Basic repository

We advise you to place those, by convention, in your bundle within a `Repository` folder.

They must implement `RomaricDrigon\OrchestraBundle\Domain\Repository\RepositoryInterface`:

```php
use RomaricDrigon\OrchestraBundle\Domain\Repository\RepositoryInterface;
use RomaricDrigon\OrchestraBundle\Annotation\Name;

class MyRepository implements RepositoryInterface
{
```

They must be declared as services, tagged with `orchestra.repository` and **indicating the corresponding entity class**:

```xml
<!-- your bundle services.xml -->
<?xml version="1.0" ?>
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="romaric_drigon_example.my_repository" class="My\Repository\Class\Path">
            <tag name="orchestra.repository" entityClass="Acme\Demo\Entity\SomeEntity" />
        </service>
        ...
```

#### Fetching Doctrine repository

An Orchestra Repository is a Symfony2 service, so you can inject it dependencies.

Orchestra can automatically resolve and inject to your service the corresponding Doctrine repository.
They will also receive a copy of Orchestra `ObjectManager`.
To benefit from this, just implement `RomaricDrigon\OrchestraBundle\Domain\Doctrine\DoctrineAwareInterface`.

For simplicity, you can extends the provided `BaseRepository` class.
You will then have access to the corresponding Doctrine repository, and we provide a generic `listing` method.

```php
use RomaricDrigon\OrchestraBundle\Domain\Doctrine\BaseRepository;
use RomaricDrigon\OrchestraBundle\Annotation\Name;

class MyRepository implements BaseRepository
{
    public function someMethod()
    {
        $objectManager = $this->objectManager;

        $doctrineRepository = $this->doctrineRepository;

        ...
```

#### Customize displayed name

The name displayed for the Repository can be automatically generated, from the class name, or optionally personalized using the `@Name` annotation.

```php
use RomaricDrigon\OrchestraBundle\Domain\Repository\RepositoryInterface;
use RomaricDrigon\OrchestraBundle\Annotation as Orchestra;

/**
 * @Orchestra\Name("CustomName")
 */
class MyRepository implements RepositoryInterface
{
```

## Hiding a method

You can add an Entity or Repository method from the menu by adding it a `@Hidden` annotation.

```php
use RomaricDrigon\OrchestraBundle\Domain\Repository\RepositoryInterface;
use RomaricDrigon\OrchestraBundle\Annotation as Orchestra;

class MyRepository implements RepositoryInterface
{
    /**
     * @Orchestra\Hidden
     */
    public function hiddenMethod()
    {
```

Note this does not work on interface, only child classes.

## Security

Orchestra fully integrates with Symfony2 Security component, and does not try to interfere with it.

As a convenience, you can add `@Security` annotations to an Entity or Repository method to restrict access to the corresponding page.
The content of this annotation is a valid Symfony2 Expression, identical to those used by the [SensioFrameworkExtraBundle](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html)

Moreover you can access Orchestra objects, such as `repository`, `entity` which an `EntityReflection` or `object` the eventual current `EntityInterface` (but not the `Request` object directly).

## Configuration

You can configure the Bundle by putting those settings in your `config.yml`:
```yaml
# app/config/config.yml
romaric_drigon_orchestra:
    app_title: Orchestra # default. Will be used as title (prefix) for pages
```

## Misc

IE8 is not supported by provided templates. Twitter Bootstrap is missing its JS polyfills, and we are using jQuery 2.0

## Thanks

Twitter Bootstrap integration have been realized using templates from [Braincrafted Bootstrap bundle](https://github.com/braincrafted/bootstrap-bundle)

Security annotation are supported in a very similar way to [SensioFrameworkExtraBundle](https://github.com/sensiolabs/SensioFrameworkExtraBundle)
