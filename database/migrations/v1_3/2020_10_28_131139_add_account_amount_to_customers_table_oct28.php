<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;

class AddAccountAmountToCustomersTableOct28 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nexopos_customers', function (Blueprint $table) {
            if ( ! Schema::hasColumn( 'nexopos_customers', 'account_amount' ) ) {
                $table->float( 'account_amount' )->default(0);
            }
        });

        if ( ! Schema::hasTable( 'nexopos_customers_account_history' ) ) {
            Schema::create( 'nexopos_customers_account_history', function( Blueprint $table ) {
                $table->bigIncrements( 'id' );
                $table->integer( 'customer_id' );
                $table->float( 'amount' )->default(0);
                $table->string( 'operation' ); // sub / add
                $table->integer( 'author' );
                $table->text( 'description' )->nullable();
                $table->timestamps();
            });
        }

        if ( ! Permission::namespace( 'nexopos.customers.manage-account' ) instanceof Permission ) {
            $permission                     =   new Permission;
            $permission->namespace          =   'nexopos.customers.manage-account';
            $permission->name               =   __( 'Manage Customers Account' );
            $permission->description        =   __( 'Allow to manage customer virtual deposit account.' );
            $permission->save();
        }

        Role::namespace( 'admin' )->addPermissions( 'nexopos.customers.manage-account' );
        Role::namespace( 'supervisor' )->addPermissions( 'nexopos.customers.manage-account' );
        Role::namespace( 'nexopos.store.administrator' )->addPermissions( 'nexopos.customers.manage-account' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nexopos_customers', function (Blueprint $table) {
            if ( Schema::hasColumn( 'nexopos_customers', 'account_amount' ) ) {
                $table->dropColumn( 'account_amount' );
            }
        });

        if ( Schema::hasTable( 'nexopos_customers_account_history' ) ) {
            Schema::dropIfExists( 'nexopos_customers_account_history' );
        }

        $permission     =   Permission::namespace( 'nexopos.customers.manage-account' );

        if ( $permission instanceof Permission ) {

            RolePermission::where( 'permission_id', $permission->id )->delete();

            $permission->delete();
        }
    }
}
