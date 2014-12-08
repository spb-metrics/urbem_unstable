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
    * Classe de mapeamento da tabela pessoal.lote_ferias
    * Data de Criação: 22/02/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TPessoalLoteFerias.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.lote_ferias
  * Data de Criação: 22/02/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalLoteFerias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalLoteFerias()
{
    parent::Persistente();
    $this->setTabela("pessoal.lote_ferias");

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote'            ,'sequence',true  ,''     ,true,false);
    $this->AddCampo('nome'                ,'varchar' ,true  ,'200'  ,false,false);
    $this->AddCampo('mes_competencia'     ,'varchar' ,true  ,'2'    ,false,false);
    $this->AddCampo('ano_competencia'     ,'varchar' ,true  ,'4'    ,false,false);
    $this->AddCampo('cod_regime','integer',true      ,''    ,false  ,"TPessoalRegime");

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT lote_ferias.*                                                                                                      \n";
    $stSql .= "     , (SELECT descricao FROM pessoal.regime WHERE cod_regime = lote_ferias.cod_regime) as regime                         \n";
    $stSql .= "     , CASE WHEN coalesce((SELECT true FROM pessoal.lote_ferias_funcao   WHERE cod_lote = lote_ferias.cod_lote LIMIT 1),false) THEN \n";
    $stSql .= "            'F'                                                                                                           \n";
    $stSql .= "            WHEN coalesce((SELECT true FROM pessoal.lote_ferias_local    WHERE cod_lote = lote_ferias.cod_lote LIMIT 1),false) THEN \n";
    $stSql .= "            'L'                                                                                                           \n";
    $stSql .= "            WHEN coalesce((SELECT true FROM pessoal.lote_ferias_orgao    WHERE cod_lote = lote_ferias.cod_lote LIMIT 1),false) THEN \n";
    $stSql .= "            'O'                                                                                                           \n";
    $stSql .= "            WHEN position('geral' in lower(nome)) > 0 THEN                                                                \n";
    $stSql .= "            'G'                                                                                                           \n";
    $stSql .= "            ELSE                                                                                                          \n";
    $stSql .= "            'C'                                                                                                           \n";
    $stSql .= "       END as tipo_filtro                                                                                                 \n";
    $stSql .= "  FROM pessoal.lote_ferias                                                                                                \n";

    return $stSql;
}

}
?>
