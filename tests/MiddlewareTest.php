<?php

namespace Rennokki\Guardian\Test;

use Illuminate\Http\Request;

use Rennokki\Guardian\Middleware\CheckPermission;

use Rennokki\Guardian\Test\Models\User;
use Rennokki\Guardian\Test\Models\Post;

use Rennokki\Guardian\Exceptions\PermissionException;

class MiddlewareTest extends TestCase {

    protected $user;
    protected $targetInstance = Post::class;
    protected $targetInstanceId = 777;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(\Rennokki\Guardian\Test\Models\User::class)->create();
    }

    public function testSimplePermissionMiddleware()
    {   
        $this->user->allow('accessTheLab');
        $this->actingAs($this->user);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('accessTheLab'));
        $this->assertEquals($this->user->allowedPermissions()->count(), 1);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 0);
        $this->assertTrue($this->user->can('accessTheLab'));
        $this->assertFalse($this->user->cannot('accessTheLab'));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });
        $response = $middleware->handle($request, function() {}, 'accessTheLab');

        $this->assertEquals($response, null);

        $this->user->disallow('accessTheLab', null, null, true);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('accessTheLab'));
        $this->assertEquals($this->user->allowedPermissions()->count(), 0);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 1);
        $this->assertFalse($this->user->can('accessTheLab'));
        $this->assertTrue($this->user->cannot('accessTheLab'));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });

        try {
            $response = $middleware->handle($request, function() {}, 'accessTheLab');
        } catch(PermissionException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGlobalPermissionMiddleware()
    {   
        $this->user->allow('edit', $this->targetInstance);
        $this->actingAs($this->user);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('edit', $this->targetInstance));
        $this->assertEquals($this->user->allowedPermissions()->count(), 1);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 0);
        $this->assertTrue($this->user->can('edit', $this->targetInstance));
        $this->assertFalse($this->user->cannot('edit', $this->targetInstance));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });
        $response = $middleware->handle($request, function() {}, 'edit', $this->targetInstance);

        $this->assertEquals($response, null);

        $this->user->disallow('edit', $this->targetInstance, null, true);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('edit', $this->targetInstance));
        $this->assertEquals($this->user->allowedPermissions()->count(), 0);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 1);
        $this->assertFalse($this->user->can('edit', $this->targetInstance));
        $this->assertTrue($this->user->cannot('edit', $this->targetInstance));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });

        try {
            $response = $middleware->handle($request, function() {}, 'edit', $this->targetInstance);
        } catch(PermissionException $e) {
            $this->assertTrue(true);
        }
    }

    public function testGlobalSpecificPermissionMiddleware()
    {   
        $this->user->allow('edit', $this->targetInstance, $this->targetInstanceId);
        $this->actingAs($this->user);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('edit', $this->targetInstance, $this->targetInstanceId));
        $this->assertEquals($this->user->allowedPermissions()->count(), 1);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 0);
        $this->assertTrue($this->user->can('edit', $this->targetInstance, $this->targetInstanceId));
        $this->assertFalse($this->user->cannot('edit', $this->targetInstance, $this->targetInstanceId));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });
        $response = $middleware->handle($request, function() {}, 'edit', $this->targetInstance, $this->targetInstanceId);

        $this->assertEquals($response, null);

        $this->user->disallow('edit', $this->targetInstance, $this->targetInstanceId, true);

        $this->assertEquals($this->user->permissions()->count(), 1);
        $this->assertTrue($this->user->hasPermission('edit', $this->targetInstance, $this->targetInstanceId));
        $this->assertEquals($this->user->allowedPermissions()->count(), 0);
        $this->assertEquals($this->user->prohibitedPermissions()->count(), 1);
        $this->assertFalse($this->user->can('edit', $this->targetInstance, $this->targetInstanceId));
        $this->assertTrue($this->user->cannot('edit', $this->targetInstance, $this->targetInstanceId));

        $request = Request::create('/', 'GET');
        $middleware = new CheckPermission;
        $request->setUserResolver(function() {
            return $this->user;
        });

        try {
            $response = $middleware->handle($request, function() {}, 'edit', $this->targetInstance, $this->targetInstanceId);
        } catch(PermissionException $e) {
            $this->assertTrue(true);
        }
    }

}