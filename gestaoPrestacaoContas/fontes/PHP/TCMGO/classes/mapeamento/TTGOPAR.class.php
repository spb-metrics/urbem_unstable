<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOPAR extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TTCMGOProjecaoAtuarial()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.projecao_atuarial");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('num_orgao');

        $this->AddCampo( 'num_orgao'  , 'integer' , true  , ''     ,true   , true  );
        $this->AddCampo( 'exercicio'  , 'varchar' , true  , '4'    ,true   , true  );
        $this->AddCampo( 'vl_receita' , 'numeric' , true  , '14,2' , false , false );
        $this->AddCampo( 'vl_despesa' , 'numeric' , true  , '14,2' , false , false );
        $this->AddCampo( 'vl_saldo'   , 'numeric' , true  , '14,2' , false , false );
    }

    public function recuperaTodos(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTodos();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaTodos()
    {
        $stSql = "SELECT
                        '10' as tipo_registro,
                        exercicio,
                        num_orgao,
                        case when lpad(trim(to_char(vl_receita, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(vl_receita, '9999999999D99')), 13, '0')
            else '0000000000,00' end as vl_receita,

                        case when lpad(trim(to_char(vl_despesa, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(vl_despesa, '9999999999D99')), 13, '0')
            else '0000000000,00' end as vl_despesa,

                        case when lpad(trim(to_char(vl_saldo, '9999999999D99')), 13, '0') != '' then
                lpad(trim(to_char(vl_saldo, '9999999999D99')), 13, '0')
            else '0000000000,00' end as vl_saldo,

                        '1' as numero_registro
                    FROM tcmgo.projecao_atuarial
                   WHERE exercicio >= '".(Sessao::getExercicio() - 1)."'";

        return $stSql;
    }
}
