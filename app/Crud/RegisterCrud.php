<?php
namespace App\Crud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Models\User;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use App\Models\Register;

class RegisterCrud extends CrudService
{
    /**
     * define the base table
     */
    protected $table      =   'nexopos_registers';

    /**
     * base route name
     */
    protected $mainRoute      =   'ns.registers';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.registers';

    /**
     * Model Used
     */
    protected $model      =   Register::class;

    /**
     * Adding relation
     */
    public $relations   =  [
        [ 'nexopos_users', 'nexopos_registers.author', '=', 'nexopos_users.id' ],
    ];

    /**
     * Define where statement
     * @var  array
    **/
    protected $listWhere    =   [];

    /**
     * Define where in statement
     * @var  array
     */
    protected $whereIn      =   [];

    protected $permissions = [
        'create' => 'nexopos.create.registers',
        'read' => 'nexopos.read.registers',
        'update' => 'nexopos.update.registers',
        'delete' => 'nexopos.delete.registers',
    ];

    /**
     * Fields which will be filled during post/put
     */
        public $fillable    =   [];

    /**
     * Define Constructor
     * @param  
     */
    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
    **/
    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Registers List' ),
            'list_description'      =>  __( 'Display all registers.' ),
            'no_entry'              =>  __( 'No registers has been registered' ),
            'create_new'            =>  __( 'Add a new register' ),
            'create_title'          =>  __( 'Create a new register' ),
            'create_description'    =>  __( 'Register a new register and save it.' ),
            'edit_title'            =>  __( 'Edit register' ),
            'edit_description'      =>  __( 'Modify  Register.' ),
            'back_to_list'          =>  __( 'Return to Registers' ),
        ];
    }

    /**
     * Check whether a feature is enabled
     * @return  boolean
    **/
    public function isEnabled( $feature )
    {
        return false; // by default
    }

    /**
     * Fields
     * @param  object/null
     * @return  array of field
     */
    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Name' ),
                // 'name'          =>  'name',
                // 'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Provide a name to the resource.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'General' ),
                    'fields'    =>  [
                        [
                            'type'  =>  'text',
                            'name'  =>  'author',
                            'label' =>  __( 'Author' ),
                            'value' =>  $entry->author ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'created_at',
                            'label' =>  __( 'Created_at' ),
                            'value' =>  $entry->created_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'description',
                            'label' =>  __( 'Description' ),
                            'value' =>  $entry->description ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'id',
                            'label' =>  __( 'Id' ),
                            'value' =>  $entry->id ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'name',
                            'label' =>  __( 'Name' ),
                            'value' =>  $entry->name ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'status',
                            'label' =>  __( 'Status' ),
                            'value' =>  $entry->status ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'updated_at',
                            'label' =>  __( 'Updated_at' ),
                            'value' =>  $entry->updated_at ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'used_by',
                            'label' =>  __( 'Used_by' ),
                            'value' =>  $entry->used_by ?? '',
                        ], [
                            'type'  =>  'text',
                            'name'  =>  'uuid',
                            'label' =>  __( 'Uuid' ),
                            'value' =>  $entry->uuid ?? '',
                        ],                     ]
                ]
            ]
        ];
    }

    /**
     * Filter POST input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPostInputs( $inputs )
    {
        return $inputs;
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, Register $entry )
    {
        return $inputs;
    }

    /**
     * Before saving a record
     * @param  Request $request
     * @return  void
     */
    public function beforePost( $request )
    {
        $this->allowedTo( 'create' );

        return $request;
    }

    /**
     * After saving a record
     * @param  Request $request
     * @param  Register $entry
     * @return  void
     */
    public function afterPost( $request, Register $entry )
    {
        return $request;
    }

    
    /**
     * get
     * @param  string
     * @return  mixed
     */
    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    /**
     * Before updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function beforePut( $request, $entry )
    {
        $this->allowedTo( 'update' );

        return $request;
    }

    /**
     * After updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function afterPut( $request, $entry )
    {
        return $request;
    }
    
    /**
     * Protect an access to a specific crud UI
     * @param  array { namespace, id, type }
     * @return  array | throw Exception
    **/
    public function canAccess( $fields )
    {
        $users      =   app()->make( Users::class );
        
        if ( $users->is([ 'admin' ]) ) {
            return [
                'status'    =>  'success',
                'message'   =>  __( 'The access is granted.' )
            ];
        }

        throw new Exception( __( 'You don\'t have access to that ressource' ) );
    }

    /**
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.registers' ) {
            $this->allowedTo( 'delete' );
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'author'  =>  [
                'label'  =>  __( 'Author' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Created_at' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'description'  =>  [
                'label'  =>  __( 'Description' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'id'  =>  [
                'label'  =>  __( 'Id' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'name'  =>  [
                'label'  =>  __( 'Name' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'status'  =>  [
                'label'  =>  __( 'Status' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'updated_at'  =>  [
                'label'  =>  __( 'Updated_at' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'used_by'  =>  [
                'label'  =>  __( 'Used_by' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'uuid'  =>  [
                'label'  =>  __( 'Uuid' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
                    ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        // Don't overwrite
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        // you can make changes here
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Edit' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'index'         =>      'id',
                'url'           =>      url( '/dashboard/' . '' . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Delete' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  url( '/api/nexopos/v4/crud/ns.registers/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Would you like to delete this ?' ),
                ]
            ]
        ];

        return $entry;
    }

    
    /**
     * Bulk Delete Action
     * @param    object Request with object
     * @return    false/array
     */
    public function bulkAction( Request $request ) 
    {
        /**
         * Deleting licence is only allowed for admin
         * and supervisor.
         */
        $user   =   app()->make( Users::class );
        if ( ! $user->is([ 'admin', 'supervisor' ]) ) {
            return response()->json([
                'status'    =>  'failed',
                'message'   =>  __( 'You\'re not allowed to do this operation' )
            ], 403 );
        }

        if ( $request->input( 'action' ) == 'delete_selected' ) {
            $status     =   [
                'success'   =>  0,
                'failed'    =>  0
            ];

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof Register ) {
                    $entity->delete();
                    $status[ 'success' ]++;
                } else {
                    $status[ 'failed' ]++;
                }
            }
            return $status;
        }

        return Hook::filter( $this->namespace . '-catch-action', false, $request );
    }

    /**
     * get Links
     * @return  array of links
     */
    public function getLinks()
    {
        return  [
            'list'      =>  'ns.registers',
            'create'    =>  'ns.registers/create',
            'edit'      =>  'ns.registers/edit/#'
        ];
    }

    /**
     * Get Bulk actions
     * @return  array of actions
    **/
    public function getBulkActions()
    {
        return Hook::filter( $this->namespace . '-bulk', [
            [
                'label'         =>  __( 'Delete Selected Groups' ),
                'identifier'    =>  'delete_selected',
                'url'           =>  route( 'crud.bulk-actions', [
                    'namespace' =>  $this->namespace
                ])
            ]
        ]);
    }

    /**
     * get exports
     * @return  array of export formats
    **/
    public function getExports()
    {
        return [];
    }
}