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
    * Classe de mapeamento da tabela FN_EXPORTACAO_PROJETO_ATIVIDADE
    * Data de Criação: 11/04/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.8  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoProjetoAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoProjetoAtividade()
{
    parent::Persistente();
    $this->setTabela('tcerj.fn_exportacao_projatv');
    $this->AddCampo('exercicio'            ,'character(4)'           ,false,'',false,false);
    $this->AddCampo('tipo'                 ,'integer'                ,false,'',false,false);
    $this->AddCampo('num_pao'              ,'integer'                ,false,'',false,false);
    $this->AddCampo('nom_pao'              ,'chacacter varying(80)'  ,false,'',false,false);
    $this->AddCampo('detalhamento'         ,'text'                   ,false,'',false,false);
    $this->AddCampo('cod_funcao'           ,'integer'                ,false,'',false,false);
    $this->AddCampo('cod_subfuncao'        ,'integer'                ,false,'',false,false);
    $this->AddCampo('cod_programa'         ,'integer'                ,false,'',false,false);
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "SELECT                                                           \n";
    $stSql .= "     *                                                           \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "     " . $this->getTabela() . "(                                 \n";
    $stSql .= "                         '".$this->getDado("stExercicio")    ."',\n";
    $stSql .= "                         '".$this->getDado("stCodEntidades") ."' \n";
    $stSql .= "                                 )                               \n";
    $stSql .= "AS tabela              (                                         \n";
    $stSql .= "          exercicio            character(4),                     \n";
    $stSql .= "          tipo                 integer,                          \n";
    $stSql .= "          num_pao              integer,                          \n";
    $stSql .= "          nom_pao              character varying(80),            \n";
    $stSql .= "          detalhamento         text,                             \n";
    $stSql .= "          cod_funcao           integer,                          \n";
    $stSql .= "          cod_subfuncao        integer,                          \n";
    $stSql .= "          cod_programa         integer                           \n";
    $stSql .= "                           )                                     \n";

    return $stSql;
}

}
