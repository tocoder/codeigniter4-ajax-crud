<?php

namespace App\Controllers;

use App\Models\BookModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class BookController extends Controller
{
    use ResponseTrait;

    /**
     * @var Model
     */
    protected $model;

    public function __construct()
    {
        $this->model = new BookModel();
    }

    /**
     * Tampilkan daftar index.
     *
     * @param \CodeIgniter\HTTP\RequestInterface
     *
     * @return \CodeIgniter\Http\Response
     */
    public function index()
    {
        return view('BookView');
    }

    /**
     * Tampilkan daftar index.
     *
     * @param \CodeIgniter\HTTP\RequestInterface
     *
     * @return \CodeIgniter\Http\Response
     */
    public function show()
    {
    }

    /**
     * Simpan resource ke database.
     *
     * @param \CodeIgniter\HTTP\RequestInterface
     *
     * @return \CodeIgniter\Http\Response
     */
    public function create()
    {
        if ($this->model->insert($this->request->getPost())) {
            return $this->respondCreated();
        }

        return $this->fail($this->model->errors());
    }

    /**
     * Tampilkan form untuk mengedit yang ditentukan.
     *
     * @param int $id
     *
     * @return CodeIgniter\Http\Response
     */
    public function edit($id)
    {
        if ($found = $this->model->find($id)) {
            return $this->respond(['data' => $found]);
        }

        return $this->fail('Failed');
    }

    /**
     * Update resource spesifik ke database.
     *
     * @param int $id
     *
     * @return CodeIgniter\Http\Response
     */
    public function update($id)
    {
        if ($this->model->update($id, $this->request->getRawInput())) {
            return $this->respondCreated();
        }

        return $this->fail($this->model->errors());
    }

    /**
     * Hapus resource spesifik ke database.
     *
     * @param int $id
     *
     * @return CodeIgniter\Http\Response
     */
    public function delete($id)
    {
        if ($found = $this->model->delete($id)) {
            return $this->respondDeleted($found);
        }

        return $this->fail('Fail deleted');
    }

    public function datatable()
    {
        if ($this->request->isAJAX()) {
            $columns = [
                1 => 'title',
                2 => 'author',
                3 => 'description',
                4 => 'status',
            ];

            $start = $this->request->getPost('start');
            $length = $this->request->getPost('length');
            $search = $this->request->getPost('search[value]');
            $order = $columns[$this->request->getPost('order[0][column]')];
            $dir = $this->request->getPost('order[0][dir]');

            return $this->respond([
                'draw'            => $this->request->getPost('draw'),
                'recordsTotal'    => $this->model->totalAll(),
                'recordsFiltered' => $this->model->countFindData($search),
                'data'            => $this->model->findPaginatedData($order, $dir, $length, $start, $search),
            ]);
        }
    }
}
