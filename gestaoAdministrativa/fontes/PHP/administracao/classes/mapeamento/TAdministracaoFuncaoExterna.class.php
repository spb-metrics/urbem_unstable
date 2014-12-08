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
* Classe de mapeamento para administracao.funcao_externa
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/**
  * Efetua conexão com a tabela  ADMINISTRACAO.FUNCAO_EXTERNA
  * Data de Criação: 09/08/2005

  * @author Analista: Cassiano de Vasconcellos Ferreira
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAdministracaoFuncaoExterna extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoFuncaoExterna()
{
    parent::Persistente();
    $this->setTabela('administracao.funcao_externa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_modulo,cod_biblioteca,cod_funcao');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',true,true);
    $this->AddCampo('cod_funcao','integer',true,'',true,true);
    $this->AddCampo('comentario','text',true,'',false,false);
    $this->AddCampo('corpo_pl','text',true,'',false,false);
    $this->AddCampo('corpo_ln','text',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                         \n";
    $stSql .= "     f.cod_modulo,                              \n";
    $stSql .= "     f.cod_biblioteca,                          \n";
    $stSql .= "     f.cod_funcao,                              \n";
    $stSql .= "     f.cod_tipo_retorno,                        \n";
    $stSql .= "     f.nom_funcao,                              \n";
    $stSql .= "     tp.nom_tipo,                               \n";
    $stSql .= "     fe.comentario,                             \n";
    $stSql .= "     fe.corpo_pl,                               \n";
    $stSql .= "     fe.corpo_ln                                \n";
    $stSql .= " FROM                                           \n";
    $stSql .= "     administracao.funcao as f,                 \n";
    $stSql .= "     administracao.tipo_primitivo as tp,        \n";
    $stSql .= "     administracao.funcao_externa as fe         \n";
    $stSql .= " WHERE                                          \n";
    $stSql .= "     f.cod_tipo_retorno = tp.cod_tipo       AND \n";
    $stSql .= "     f.cod_modulo       = fe.cod_modulo     AND \n";
    $stSql .= "     f.cod_biblioteca   = fe.cod_biblioteca AND \n";
    $stSql .= "     f.cod_funcao       = fe.cod_funcao         \n";

    return $stSql;
}
}
