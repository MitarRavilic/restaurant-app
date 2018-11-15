<?php
    namespace App\Models;

    use App\Core\Model;
    use App\Core\Field;

    use App\Validators\NumberValidator;
    use App\Validators\StringValidator;

    class CategoryModel extends Model {
        protected function getFieldList(): array {
            return [
                'category_id' => new Field((new NumberValidator())
                                            ->allowUnsigned()
                                            ->setMaxIntegerDigitCount(11), false),
                'title'        => new Field((new StringValidator())
                                            ->setMinLength(0)
                                            ->setMaxLength(255))
            ];
        }

        public function addCategory(string $title) {
            $sql = 'CALL sp_createCategory(?)';
            $prep = $this->getConnection()->prepare($sql);
            return $prep->execute([$title]);

            $res ? $this->set('message','Category successfully added.') : $this->set('message','Category creation failed.');
        }
        public function updateCategory(int $category_id, string $title) {
            $sql = 'CALL sp_updateCategory(?, ?)';
            $prep = $this->getConnection()->prepare($sql);
            return $prep->execute([$category_id, $title]);
            
            $res ? 'Category successfully updated.' : 'Category update failed.';
        }

        final public function deleteCategory(int $category_id){
            $sql = 'CALL sp_deleteCategory(?)';
            $prep = $this->getConnection()->prepare($sql);
            return $prep->execute([$category_id]);

            $res ? 'Category successfully deleted.' : 'Category deletion failed.';
        }
    }
