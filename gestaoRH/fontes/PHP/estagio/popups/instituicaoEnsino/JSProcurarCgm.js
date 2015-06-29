<script type="text/javascript">
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
</script>
<?php
/**
    * Arquivo de filtro de busca de CGM de Instituição/Entidade
    * Data de Criação: 03/10/2006
    
    
    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @package URBEM
    * @subpackage 
    
    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-03 08:08:21 -0300 (Ter, 03 Out 2006) $
    
    * Casos de uso: uc-04.07.01
*/

/*
$Log$
Revision 1.1  2006/10/03 11:08:21  souzadl
construção


*/

?>

<script type="text/javascript">

function habilitaCampos( tipoHabilita ){
    var f = document.frm;

    //habilita campos para Pessoa Fisica
    if( tipoHabilita == 'F' ){
        f.stCPF.disabled = false;
        f.stCNPJ.value = '';
        f.stCNPJ.disabled = true;
        f.stNomeFantasia.value = '';
        f.stNomeFantasia.disabled = true;
    }

    //habilita campos para Pessoa Juridica
    if( tipoHabilita == 'J' ){
        f.stCNPJ.disabled = false;
        f.stNomeFantasia.disabled = false;
        f.stCPF.value = '';
        f.stCPF.disabled = true;
    }

    //habilita campos para Todos os tipos de Pessoa
    if( tipoHabilita == 'T' ){
        f.stCPF.value = '';
        f.stCNPJ.value = '';
        f.stNomeFantasia.value = '';
        f.stCPF.disabled = true;
        f.stCNPJ.disabled = true;
        f.stNomeFantasia.disabled = true;
    }
}

</script>
