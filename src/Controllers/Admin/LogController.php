<?php

namespace Stevebauman\Logs\Controllers\Admin;

use Stevebauman\LogReader\LogReader;
use Platform\Access\Controllers\AdminController;

/**
 * Class LogController
 * @package Stevebauman\Logs\Controllers\Admin
 */
class LogController extends AdminController
{
    /**
     * @var LogReader
     */
    protected $reader;

    /**
     * The available mass log actions
     *
     * @var array
     */
    protected $actions = [
        'delete',
        'read',
    ];

    /**
     * @param LogReader $reader
     */
    public function __construct(LogReader $reader)
    {
        parent::__construct();

        $this->reader = $reader;
    }

    /**
     * Displays the sites log entries
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('stevebauman/logs::index');
    }

    /**
     * Processes displaying the data to the grid
     *
     * @return DataGrid
     * @throws \Stevebauman\LogReader\Exceptions\UnableToRetrieveLogFilesException
     */
    public function grid()
    {
        $columns = [
            'id',
            'level',
            'date',
            'header',
        ];

        $settings = [
            'sort'      => 'date',
            'direction' => 'desc',
            'pdf_view'  => 'pdf',
        ];

        $transformer = function($element)
        {
            $element['level'] = $this->levelToLabel($element['level']);
            $element['show_url'] = route('admin.logs.show', array($element['id']));
            $element['header'] = str_limit($element['header']);

            return $element;
        };

        $reader = $this->reader;

        $filters = request()->input('filters');

        if(is_array($filters))
        {
            /*
             * If an include_read filter is toggled, make sure
             * we toggle it on the log reader
             */
            foreach($filters as $filter)
            {
                if(array_key_exists('include_read', $filter)) $reader->includeRead();
            }
        }

        return datagrid($reader->get(), $columns, $settings, $transformer);
    }

    /**
     * Displays the log entry with the specified ID
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $entry = $this->reader->find($id);

        return view('stevebauman/logs::show', compact('entry'));
    }

    /**
     * Marks the specified log entry as read
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function read($id)
    {
        $this->reader->find($id)->markRead();

        $this->alerts->success('Successfully marked log entry as read');

        return redirect()->route('admin.logs.index');
    }

    /**
     * Deletes the specified log entry
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->reader->find($id)->delete();

        $this->alerts->success('Successfully deleted log entry');

        return redirect()->route('admin.logs.index');
    }

    /**
     * Executes a mass action
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function executeAction()
    {
        $action = request()->input('action');

        if (in_array($action, $this->actions))
        {
            foreach (request()->input('rows', []) as $row)
            {
                if( ! empty($row))
                {
                    $entry = $this->reader->find($row);

                    switch($action)
                    {
                        case 'delete':
                            $entry->delete();
                            break;
                        case 'read':
                            $entry->markRead();
                            break;
                    }
                }
            }

            return response('Success');
        }

        return response('Failed', 500);
    }

    /**
     * Transforms the specified level into a bootstrap
     * compatible label
     *
     * @param $level
     * @return string
     */
    private function levelToLabel($level)
    {
        $label = '<span class="label label-%s">%s</span>';

        switch($level)
        {
            case 'error':
                return sprintf($label, 'danger', ucfirst($level));
            case 'critical':
                return sprintf($label, 'danger', ucfirst($level));
            case 'warning':
                return sprintf($label, 'warning', ucfirst($level));
            case 'alert':
                return sprintf($label, 'warning', ucfirst($level));
            case 'notice':
                return sprintf($label, 'info', ucfirst($level));
            case 'info':
                return sprintf($label, 'info', ucfirst($level));
            case 'debug':
                return sprintf($label, 'primary', ucfirst($level));
            default:
                return sprintf($label, 'default', ucfirst($level));
        }
    }

}