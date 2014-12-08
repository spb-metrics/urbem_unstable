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

/*
	* Classe de mapeamento da tabela tcemg.contrato_apostila
	* Data de Criação   : 14/04/2014

	* @author Analista      Silvia Martins Silva
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContratoApostila.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContratoApostila extends Persistente
{
    /**
            * Método Construtor
            * @access Private
    */
    function TTCEMGContratoApostila()
    {
        parent::Persistente();
        $this->setTabela('tcemg.contrato_apostila');
        
        $this->setCampoCod('');
        $this->setComplementoChave('cod_apostila,cod_contrato,exercicio, cod_entidade');
        
        $this->AddCampo( 'cod_apostila'     , 'integer' , true  , ''    , true  , false );
        $this->AddCampo( 'cod_contrato'     , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'exercicio'        , 'char'    , true  , '4'   , true  , true  );
        $this->AddCampo( 'cod_entidade'     , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'cod_tipo'         , 'integer' , true  , ''    , false , false );
        $this->AddCampo( 'cod_alteracao'    , 'integer' , true  , ''    , false , false );
        $this->AddCampo( 'descricao'        , 'varchar' , true  , '250' , false , false );
        $this->AddCampo( 'data_apostila'    , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'valor_apostila'   , 'numeric' , true  , '14,2', false , false );
    }
    
    public function recuperaContratoApostila(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoApostila().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContratoApostila()
    {
        $stSql = "SELECT 
            contrato.cod_entidade,
            contrato.nro_contrato,
            contrato.exercicio,
            contrato.objeto_contrato,
            TCA.cod_apostila,
            TCA.exercicio AS exercicio_apostila,
            to_char(TCA.data_apostila, 'dd/mm/yyyy') AS data_apostila
            FROM tcemg.contrato_apostila AS TCA
            INNER JOIN tcemg.contrato
            ON TCA.cod_contrato=contrato.cod_contrato
            AND TCA.exercicio=contrato.exercicio
            AND TCA.cod_entidade=contrato.cod_entidade
        ";

        return $stSql;
    }
	
	public function __destruct(){}

}
?>
